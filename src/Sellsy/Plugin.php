<?php

namespace UniAlteri\Sellsy\Wordpress;

use UniAlteri\Sellsy\Client\Client;
use UniAlteri\Sellsy\Wordpress\Form\CustomField;
use UniAlteri\Sellsy\Wordpress\Type\Prospect;

/**
 * Class Plugin
 * Class to manage plugin's features
 * @package UniAlteri\Sellsy\Wordpress
 */
class Plugin
{
    /**
     * @var OptionsBag
     */
    protected $options;

    /**
     * @var Client
     */
    protected $sellsyClient;

    /**
     * @param Client $sellsyClient
     * @param OptionsBag $options
     */
    public function __construct($sellsyClient, $options)
    {
        //Initialize plugin var
        $this->sellsyClient = $sellsyClient;
        $this->options = $options;

        //Configure Sellsy
        $this->configureSellsy();
    }

    /**
     * Check if the Curl extensions is available in the plateform
     * @return true
     */
    public function checkCUrlExtensions()
    {
        if (!in_array('curl', get_loaded_extensions())) {
            return false;
        }

        return true;
    }

    /**
     * To load translation for this modules
     * @return $this
     */
    public function loadTranslation()
    {
        \load_plugin_textdomain(
            'wpsellsy',
            true,
            SELLSY_WP_PATH_LANG
        );

        return $this;
    }

    /**
     * To uninstall this plugin
     * @return $this
     */
    public function disablePlugin()
    {
        \delete_option(OptionsBag::WORDPRESS_SETTINGS_NAME);

        return $this;
    }

    /**
     * Return the option's manager of this plugin
     * @return OptionsBag
     */
    public function getOptionsBag()
    {
        return $this->options;
    }

    /**
     * Get the Sellsy client to use the API
     * @return Client
     */
    public function getSellsyClient()
    {
        return $this->sellsyClient;
    }

    /**
     * To configure Sellsy with registered credentials
     * @return $this
     */
    protected function configureSellsy()
    {
        //Configure the client
        $this->sellsyClient->setApiUrl(SELLSY_WP_API_URL);
        $this->sellsyClient->setOAuthAccessToken($this->options['WPIutilisateur_token']);
        $this->sellsyClient->setOAuthAccessTokenSecret($this->options['WPIutilisateur_secret']);
        $this->sellsyClient->setOAuthConsumerKey($this->options['WPIconsumer_token']);
        $this->sellsyClient->setOAuthConsumerSecret($this->options['WPIconsumer_secret']);

        return $this;
    }

    /**
     * To check if Sellsy credentials are goods
     * @return bool
     */
    public function checkSellsyCredentials()
    {
        $this->configureSellsy();

        try {
            $result = $this->sellsyClient->getInfos();
            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return the list of fields
     * @param string $for
     * @return CustomField[]
     */
    public function listCustomFields($for='prospect')
    {
        $final = [];

        switch ($for) {
            case 'prospect':
                $prospectType = new Prospect();
                $final = $prospectType->getStandardFields();
                break;
        }

        $customFields = $this->sellsyClient->customFields()->getList(['search'=>['useOn'=>(array)$for]]);
        foreach ($customFields->response->result as $customFields) {
            $final[$customFields->id] = new CustomField(
                $customFields->id,
                $customFields->type,
                $customFields->name,
                $customFields->code,
                $customFields->description,
                $customFields->defaultValue,
                $customFields->prefsList
            );
        }

        return $final;
    }

    /**
     * @return CustomField[]
     */
    public function listSelectedFields()
    {
        $element = 'prospect';
        if (isset($this->options['WPIcreer_prospopp'])) {
            switch ($this->options['WPIcreer_prospopp']) {
                case 'prospectOnly':
                case 'prospectOpportunity':
                    $element = 'prospect';
                    break;
            }
        }

        $selectedFields = $this->options['WPIFieldsSelected'];
        if (empty($selectedFields)) {
            return [];
        }

        $final = [];
        foreach ($this->listCustomFields($element) as $name=>$field) {
            if (in_array($name, $selectedFields)) {
                $final[$field->getCode()] = $field;
            }
        }

        return $final;
    }

    /**
     * Check if a opportunity source exist in the sellsy account
     * @param string $label
     * @return bool
     */
    public function checkOppSource($label)
    {
        try {
            $sources = $this->sellsyClient->opportunities()->getSources();
            foreach ($sources->response as $source) {
                if (strcasecmp($source->label, $label) == 0) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Method to create a opportunitiy source via the wordpress admin
     */
    public function createOppSource()
    {
        //Retrieve the Nonce/XSRF id to check its validity
        $nonce = null;
        if (isset($_POST['nonce'])) {
            $nonce = $_POST['nonce'];
        }

        //Check Nonce/XSRF to avoid attacks
        if (!\wp_verify_nonce($nonce, 'slswp_ajax_nonce')) {
            \wp_die(__('Accès interdit', 'wpsellsy'));
        }

        //Check if the
        if (isset($_POST['action']) && 'sls_createOppSource' == $_POST['action']
            && isset($_POST['param']) && 'creerSource' == $_POST['param']) {

            //Create the source via the client
            try {
                $source = $this->options['WPInom_opp_source'];
                $result = $this->sellsyClient->opportunities()->createSource(['source' => ['label' => $source]]);

                if (!empty($result->response)) {
                    //Successful
                    echo 'true';
                    return;
                }
            } catch (\Exception $e) {
                //Failure
                echo 'false';
                return;
            }
        }

        //Failure
        echo 'false';
    }

    /**
     * Create prospect
     * @param array $formValues
     * @return int|array
     */
    public function createProspect(array &$formValues)
    {
        $prospectType = new Prospect();

        $errors = [];

        //Extract fields, validate them and prepare registering
        $params = [];

        $mandatoryFields = array_flip($this->options['WPIMandatoryFields']);
        foreach ($formValues as $key=>$fieldValue) {
            try {
                $prospectType->validateField($key, $fieldValue, $mandatoryFields);
                $prospectType->populateParams($key, $fieldValue, $params);
            } catch (\Exception $e) {
                $errors[$key] = $e->getMessage();
            }
        }

        //Register prospect if no error
        if (empty($errors)) {
            try {
                return $this->sellsyClient->prospects()->create($params)->response;
            } catch (\Exception $e) {
                return [$e->getMessage()];
            }
        } else {
            return $errors;
        }
    }

    /**
     * Get next value to use for the opportunity
     * @return int
     */
    public function getOpportunityCurrentIdent()
    {
        return $this->sellsyClient->opportunities()->getCurrentIdent()->response;
    }

    /**
     * Get the default funnel id configured in Sellsy to register new opportunity
     * @return int|null
     */
    public function getFunnelId()
    {
        $funnelsList = $this->sellsyClient->opportunities()->getFunnels()->response;
        $pipelineId = null;
        foreach ($funnelsList as $key=>$funnel) {
            if (is_object($funnel) && 'default' == $funnel->name) {
                $pipelineId = $funnel->id;
                break;
            } elseif ('defaultFunnel' == $key) {
                $pipelineId = intval($funnel);
                break;
            }
        }

        return $pipelineId;
    }

    /**
     * Get the default step id for the passed funnel)
     * @param int $funnelId
     * @return null|int
     */
    public function getStepId($funnelId)
    {
        $stepId = null;
        if (!empty($funnelId)) {
            $stepsList = $this->sellsyClient->opportunities()->getStepsForFunnel(['funnelid' => $funnelId])->response;

            foreach ($stepsList as $key => $step) {
                $stepId = $step->id;
                break;
            }
        }

        return $stepId;
    }

    /**
     * Get the source id from it's name (can be configured in admin)
     * @param string $sourceName
     * @return int|null
     */
    public function getSourceId($sourceName)
    {
        $sourceId = null;
        $sourcesList = $this->sellsyClient->opportunities()->getSources()->response;
        foreach ($sourcesList as $key=>$source) {
            if (!empty($source->label) && $sourceName == $source->label){
                $sourceId = $source->id;
                break;
            }
        }

        return $sourceId;
    }

    /**
     * Method to create an opportunity when a prospect is created
     * @param int $prospectId
     * @param string $sourceName
     * @param string $note
     * @return int|null
     */
    public function createOpportunity($prospectId, $sourceName, $note='')
    {
        //Retrieve needed id to create the opportunity
        $lastOpportunityId = $this->getOpportunityCurrentIdent();
        $funnelId = $this->getFunnelId();
        $sourceId = $this->getSourceId($sourceName);
        $stepId = $this->getStepId($funnelId);
        $date = strtotime('+1 week', time());

        //Register it
        return $this->sellsyClient->opportunities()->create(
            [
                'opportunity' => [
                    'linkedtype' => 'prospect',
                    'linkedid' => $prospectId,
                    'ident' => $lastOpportunityId,
                    'sourceid' => $sourceId,
                    'dueDate' => $date,
                    'name' => __('Contact site web', 'wpsellsy'),
                    'funnelid' => $funnelId,
                    'stepid' => $stepId,
                    'brief' => $note
                ]
            ]
        )->response;
    }
}
<?php
/**
 * Sellsy Wordpress plugin.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

namespace UniAlteri\Sellsy\Wordpress;

use UniAlteri\Sellsy\Client\Client;
use UniAlteri\Sellsy\Client\ClientInterface;
use UniAlteri\Sellsy\Wordpress\Form\Field;
use UniAlteri\Sellsy\Wordpress\Form\Settings;
use UniAlteri\Sellsy\Wordpress\Type\Prospect;

/**
 * Class Plugin
 * Class to manage plugin's features.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Plugin
{
    /**
     * Object to access and store all dynamics parameters neededby this plugin
     * @var OptionsBag
     */
    protected $options;

    /**
     * Client to communicate with the Sellsy API
     * @var ClientInterface
     */
    protected $sellsyClient;

    /**
     * List of fields (standard and custom) defined by sellsy's types
     * @var array
     */
    protected $fieldsByTypes = array();

    /**
     * Constructor
     * @param ClientInterface $sellsyClient
     * @param OptionsBag      $options
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
     * Check if the Curl extensions is available in the plateform.
     *
     * @return true
     */
    public function checkCUrlExtensions()
    {
        return (bool) in_array('curl', get_loaded_extensions());
    }

    /**
     * To load translation for this modules.
     *
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
     * To uninstall this plugin.
     *
     * @return $this
     */
    public function disablePlugin()
    {
        \delete_option(OptionsBag::WORDPRESS_SETTINGS_NAME);

        return $this;
    }

    /**
     * Return the option's manager of this plugin.
     *
     * @return OptionsBag
     */
    public function getOptionsBag()
    {
        return $this->options;
    }

    /**
     * Get the Sellsy client to use the API.
     *
     * @return Client
     */
    public function getSellsyClient()
    {
        return $this->sellsyClient;
    }

    /**
     * To configure Sellsy with registered credentials.
     *
     * @return $this
     */
    protected function configureSellsy()
    {
        //Configure the client
        $this->sellsyClient->setApiUrl(SELLSY_WP_API_URL);
        $this->sellsyClient->setOAuthAccessToken($this->options[Settings::ACCESS_TOKEN]);
        $this->sellsyClient->setOAuthAccessTokenSecret($this->options[Settings::ACCESS_SECRET]);
        $this->sellsyClient->setOAuthConsumerKey($this->options[Settings::CONSUMER_TOKEN]);
        $this->sellsyClient->setOAuthConsumerSecret($this->options[Settings::CONSUMER_SECRET]);

        return $this;
    }

    /**
     * To check if Sellsy credentials are goods.
     *
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
     * Return the list of fields.
     *
     * @param string $for
     *
     * @return Field[]
     */
    public function listFields($for = 'prospect')
    {
        if (isset($this->fieldsByTypes[$for])) {
            //To avoid multiple request
            return $this->fieldsByTypes[$for];
        }

        $final = array();

        switch ($for) {
            case 'prospect':
                $prospectType = new Prospect();
                $final = $prospectType->getStandardFields();
                break;
        }

        try {
            $customFields = $this->sellsyClient
                ->customFields()
                ->getList(
                    array(
                        'search' => array(
                            'useOn' => (array) $for,
                        ),
                    )
                );

            foreach ($customFields->response->result as $customFields) {
                $final[$customFields->code] = new Field(
                    $customFields->id,
                    $customFields->type,
                    $customFields->name,
                    $customFields->code,
                    $customFields->description,
                    $customFields->defaultValue,
                    $customFields->prefsList,
                    true,
                    ($customFields->isRequired == 'Y')
                );
            }
        } catch (\Exception $e) {
            add_settings_error(
                OptionsBag::WORDPRESS_SETTINGS_NAME,
                'sellSyTokens',
                __('Erreur: Connexion à l\'API Sellsy impossible. Les tokens saisis sont incorrects.', 'wpsellsy'),
                'error'
            );
        }

        //Backup in local cache to avoid multiple api request for this execution
        $this->fieldsByTypes[$for] = $final;

        return $final;
    }

    /**
     * Return all required custom field : Sellsy can not add to an entity some custom fields
     * if there are some missing required fields...
     *
     * @param string $for
     *
     * @return Field[]
     */
    public function listRequiredCustomsFields($for = 'prospect')
    {
        $final = array();
        foreach ($this->listFields($for) as $code => $field) {
            if ($field->isRequiredField() && $field->isCustomField()) {
                $final[$field->getId()] = $field;
            }
        }

        return $final;
    }

    /**
     * Get the list of type's fields selected by the administrator to display in HTML form
     * @return Field[]
     */
    public function listSelectedFields()
    {
        $element = 'prospect';
        if (isset($this->options[Settings::OPPORTUNITY_CREATION])) {
            switch ($this->options[Settings::OPPORTUNITY_CREATION]) {
                case 'prospectOnly':
                case 'prospectOpportunity':
                    $element = 'prospect';
                    break;
            }
        }

        $selectedFields = $this->options[Settings::FIELDS_SELECTED];
        if (empty($selectedFields)) {
            return array();
        }

        $availableFields = $this->listFields($element);
        $final = array();
        foreach ($selectedFields as $name) {
            if (isset($availableFields[$name])) {
                $final[$name] = $availableFields[$name];
            }
        }

        return $final;
    }

    /**
     * Return the list of defined sources.
     *
     * @return string[]
     */
    public function getSourcesList()
    {
        $sourcesListString = $this->options[Settings::OPPORTUNITY_SOURCE];
        if (empty($sourcesListString)) {
            return array();
        }

        return explode(',', $sourcesListString);
    }

    /**
     * Check if all sources are defined.
     *
     * @param array $sourcesLabelsList
     *
     * @return array
     */
    public function checkOppListSources($sourcesLabelsList)
    {
        $final = array();
        foreach ($sourcesLabelsList as $sourceLabel) {
            $final[$sourceLabel] = $this->checkOppSource($sourceLabel);
        }

        return $final;
    }

    /**
     * Check if a opportunity source exist in the sellsy account.
     *
     * @param string $label
     *
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
     * Method to create a opportunity source via the Wordpress admin.
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
            \wp_die(__('Forbidden access', 'wpsellsy'));

            return;
        }

        //Check if the
        if (isset($_POST['action']) && 'sls_createOppSource' == $_POST['action']
            && isset($_POST['param']) && 'creerSource' == $_POST['param']) {

            //Create the source via the client
            try {
                $source = $_POST['source'];
                $result = $this->sellsyClient->opportunities()
                    ->createSource(
                        array(
                            'source' => array('label' => $source)
                        )
                    );

                if (!empty($result->response)) {
                    //Successful
                    \wp_die('true');

                    return;
                }
            } catch (\Exception $e) {
                //Failure
                \wp_die('false');

                return;
            }
        }

        //Failure
        \wp_die('false');
    }

    /**
     * Method to find missing required customs fields to populate the new record with default value to avoid error.
     *
     * @param string $for          entity type in Sellsy
     * @param array  $customValues already populated values
     */
    protected function addMissingRequiredField($for, &$customValues)
    {
        //Exclude already populated custom type
        $requiredFields = $this->listRequiredCustomsFields($for);
        foreach ($customValues as &$value) {
            if (isset($requiredFields[$value['cfid']])) {
                unset($requiredFields[$value['cfid']]);
            }
        }

        //Add missing fields
        foreach ($requiredFields as $field) {
            $customValues[] = array(
                'cfid' => $field->getId(),
                'value' => $field->getDefaultValue()
            );
        }
    }

    /**
     * Create a new prospect with value passed by $formValues. This method accept also a string by reference whom will
     * be populated with prospect's validated values.
     *
     * @param array  $formValues
     * @param string $body       output body used for email notification
     *
     * @return int|array
     */
    public function createProspect(array &$formValues, &$body)
    {
        $prospectType = new Prospect();

        $errors = array();

        //Extract fields, validate them and prepare registering
        $params = array();

        $mandatoryFields = array_flip((array) $this->options[Settings::MANDATORIES_FIELDS]);
        $selectedFields = $this->listSelectedFields();

        $customValues = array();
        //Browse all form's fields
        foreach ($formValues as $key => $fieldValue) {
            $originalValue = $fieldValue;
            try {
                //Check field validity
                $prospectType->validateField($key, $fieldValue, $mandatoryFields);

                if (isset($selectedFields[$key])) {
                    $field = $selectedFields[$key];

                    //Manage boolean fields
                    if ('boolean' == $field->getType()) {
                        if ('Y' != $fieldValue) {
                            $fieldValue = 'N';
                        }
                    }

                    if ($field->isCustomField()) {
                        //Is custom field, keep to save them after
                        $customValues[] = array('cfid' => $field->getId(), 'value' => $fieldValue);
                    }

                    //Update mail body
                    $body .= $field->getName().' : '.$originalValue.'<br/>'.PHP_EOL;
                }

                //Convert to API Param
                $prospectType->populateParams($key, $fieldValue, $params, $body);
            } catch (\Exception $e) {
                $errors[$key] = $e->getMessage();
            }
        }

        //Register prospect if no error
        if (empty($errors)) {
            try {
                $prospectId = $this->sellsyClient->prospects()->create($params)->response;

                if (!empty($customValues)) {
                    $this->addMissingRequiredField('prospect', $customValues);

                    //Save custom fields
                    $this->sellsyClient->customFields()->recordValues(
                        array(
                            'linkedtype' => 'prospect',
                            'linkedid' => $prospectId,
                            'values' => $customValues,
                        )
                    );
                }

                return $prospectId;
            } catch (\RuntimeException $e) {
                return array($e->getMessage());
            } catch (\Exception $e) {
                return array($e->getMessage());
            }
        } else {
            return $errors;
        }
    }

    /**
     * Get next value to use for the opportunity.
     *
     * @return int
     */
    public function getOpportunityCurrentIdent()
    {
        return $this->sellsyClient->opportunities()->getCurrentIdent()->response;
    }

    /**
     * Get the default funnel id configured in Sellsy to register new opportunity.
     *
     * @return int|null
     */
    public function getFunnelId()
    {
        $funnelsList = $this->sellsyClient->opportunities()->getFunnels()->response;
        $pipelineId = null;
        foreach ($funnelsList as $key => $funnel) {
            if (is_object($funnel) && 'default' === $funnel->name) {
                $pipelineId = $funnel->id;
                break;
            } elseif ('defaultFunnel' === $key) {
                $pipelineId = intval($funnel);
                break;
            }
        }

        return $pipelineId;
    }

    /**
     * Get the default step id for the passed funnel).
     *
     * @param int $funnelId
     *
     * @return null|int
     */
    public function getStepId($funnelId)
    {
        $stepId = null;
        if (!empty($funnelId)) {
            $stepsList = $this->sellsyClient->opportunities()
                ->getStepsForFunnel(
                    array(
                        'funnelid' => $funnelId
                    )
                )->response;

            foreach ($stepsList as $step) {
                $stepId = $step->id;
                break;
            }
        }

        return $stepId;
    }

    /**
     * Get the source id from it's name (can be configured in admin).
     *
     * @param string $sourceName
     *
     * @return int|null
     */
    public function getSourceId($sourceName)
    {
        $sourceId = null;
        $sourcesList = $this->sellsyClient
            ->opportunities()
            ->getSources()
            ->response;

        foreach ($sourcesList as $source) {
            if (!empty($source->label) && $sourceName == $source->label) {
                $sourceId = $source->id;
                break;
            }
        }

        return $sourceId;
    }

    /**
     * Method to create an opportunity when a prospect is created.
     *
     * @param int    $prospectId
     * @param string $sourceName
     * @param string $note
     *
     * @return int|null
     */
    public function createOpportunity($prospectId, $sourceName, $note = '')
    {
        //Retrieve needed id to create the opportunity
        $lastOpportunityId = $this->getOpportunityCurrentIdent();
        $funnelId = $this->getFunnelId();
        $sourceId = $this->getSourceId($sourceName);
        $stepId = $this->getStepId($funnelId);
        $date = strtotime('+1 week', time());

        //Register it
        return $this->sellsyClient->opportunities()->create(
            array(
                'opportunity' => array(
                    'linkedtype' => 'prospect',
                    'linkedid' => $prospectId,
                    'ident' => $lastOpportunityId,
                    'sourceid' => $sourceId,
                    'dueDate' => $date,
                    'name' => __('Contact from', 'wpsellsy').' '.$sourceName,
                    'funnelid' => $funnelId,
                    'stepid' => $stepId,
                    'brief' => $note,
                ),
            )
        )->response;
    }

    /**
     * Send a mail to contact.
     *
     * @param string $body
     *
     * @return bool
     */
    public function sendMail($body)
    {
        //Get mailer from Wordpress
        require_once ABSPATH.WPINC.'/class-phpmailer.php';
        require_once ABSPATH.WPINC.'/class-smtp.php';

        //Initialiez mailer
        $domain = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
        $mail = new \PHPMailer();

        //Configure new mail
        $mail->SetFrom('sellsy-form@'.$domain, 'Sellsy Plugin');
        $mail->AddAddress($this->options[Settings::SUBMIT_NOTIFICATION]);
        if (!empty($this->options[Settings::FORM_NAME])) {
            $mail->Subject = $this->options[Settings::FORM_NAME];
        } else {
            $mail->Subject = __('Formulaire site web: Demande d\'informations', 'wpsellsy');
        }
        $mail->MsgHTML($body);
        $mail->CharSet = 'UTF-8';

        //Send email
        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }
}

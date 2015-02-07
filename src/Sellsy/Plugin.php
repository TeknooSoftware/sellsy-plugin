<?php

namespace UniAlteri\Sellsy\Wordpress;

use UniAlteri\Sellsy\Client\Client;
use UniAlteri\Sellsy\Wordpress\Form\CustomField;
use UniAlteri\Sellsy\Wordpress\OptionsBag;

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
        if (isset($_POST['action']) && 'slswp_createOppSource' == $_POST['action']
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
}
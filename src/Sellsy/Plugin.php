<?php

namespace UniAlteri\Sellsy\Wordpress;

use UniAlteri\Sellsy\Client\Client;
use UniAlteri\Sellsy\OptionsBag;

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
        load_plugin_textdomain(
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
        delete_option(OptionsBag::WORDPRESS_SETTINGS_NAME);

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
        $this->sellsyClient->setApiUrl(SELLSY_WP_WEBAPI_URL);
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
     * Check if a opportunity source exist in the sellsy account
     * @param string $label
     * @return bool
     */
    public function checkOppSource($label)
    {
        try {
            $sources = $this->sellsyClient->opportunities->getSources();
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

    function createOppSource() {

        $nonce = $_POST['nonce'];

        if (!wp_verify_nonce($nonce, 'wpi_ajax_nonce'))
            die (__('Accès interdit', 'wpsellsy'));

        if (isset($_POST['action']) AND $_POST['action'] == 'wpi_createOppSource'
            AND isset($_POST['param']) AND $_POST['param'] == 'creerSource') {

            $options = get_option('wpsellsy_options');
            $source = $options['WPInom_opp_source'];

            $request = array(
                'method' => 'Opportunities.createSource',
                'params' => array(
                    'source'	=> array(
                        'label'		=> $source
                   )
               )
           );

            $creersource = sellsyConnect_curl::load()->requestApi($request);
            if ($creersource->response != '')
                echo 'true';
            else
                echo 'false';
            die();
        }

    }
}
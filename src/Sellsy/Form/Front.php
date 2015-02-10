<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Front
 * Class to configure Wordpress front to use this plugin
 * @package UniAlteri\Sellsy\Form
 */
class Front
{
    /**
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * @var OptionsBag
     */
    protected $options;

    /**
     * TO count usage of shortcode in a single request
     * @var int
     */
    protected static $formCounter = 0;

    /**
     * @param Plugin $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct($sellsyPlugin, $options)
    {
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;
    }

    /**
     * To declare needed css in front
     */
    public function addCSS()
    {
        if (!\is_admin()) {
            \wp_register_style(
                'wpsellsystyles',
                \plugins_url('/css/wp_sellsy.css', SELLSY_WP_PATH_FILE),
                array(),
                '1.0',
                'screen'
            );

            \wp_enqueue_style('wpsellsystyles');
        }
    }

    /**
     * @param array $selectedFields
     * @param array $attr of the short code
     * @param string $formId to ignore request if this request is not destinate to this form
     * @return null|int id of the prospect if it was created
     */
    protected function validateForm(array &$selectedFields, $attr=array(), $formId)
    {
        $postValues = $_POST;
        if (!\is_admin() //We are in front
            && isset($postValues['send_wp_sellsy']) //The form was sent
            && isset($postValues['slswp_nonce_verify_page'])) { //Nonce/Xsrf is present
            if (\wp_verify_nonce($postValues['slswp_nonce_verify_page'], 'slswp_nonce_field')) { //Nonce is valid

                if (!isset($postValues['formId']) || $postValues['formId'] != $formId) {
                    //Not good form
                    return null;
                }

                $postValues = array_intersect_key($postValues, $selectedFields);
                $prospectReturn = $this->sellsyPlugin->createProspect($postValues, $body);

                if (is_numeric($prospectReturn)) {
                    if ('prospectOpportunity' == $this->options[Settings::OPPORTUNITY_CREATION]) {
                        $source = $this->extractSource($attr);
                        $this->sellsyPlugin->createOpportunity($prospectReturn, $source, '');
                    }

                    if (!empty($this->options[Settings::SUBMIT_NOTIFICATION]) && !empty($body)) {
                        $this->sellsyPlugin->sendMail($body);
                    }

                    return true;
                } else {
                    return $prospectReturn;
                }
            }
        }

        return null;
    }

    /**
     * Return the source to use to create opportunity
     * @param array $attr
     * @return string
     */
    protected function extractSource($attr)
    {
        //Get source list from plugin
        $sourcesList = $this->sellsyPlugin->getSourcesList();

        //admin has defined source in short code, verify it and ue it
        if (isset($attr['source'])) {
            if (in_array($attr['source'], $sourcesList)) {
                return $attr['source'];
            }
        }

        //Else use the default source list
        reset($sourcesList);
        return current($sourcesList);
    }

    /**
     * Return the form id via attribute. If there are no form id, compute it
     * @param array $attr
     * @return string;
     */
    protected function getFormId($attr)
    {
        //Increase counter
        self::$formCounter++;

        //Admin has already defined an id, use it
        if (isset($attr['formId'])) {
            return $attr['formId'];
        }

        //Else compute it
        return 'wpSellsyForm'.self::$formCounter;
    }

    /**
     * To compute shortcode insert in a page
     * @param string $attr
     * @param null|mixed $content
     */
    public function shortcode($attr, $content = null)
    {
        $formId = $this->getFormId($attr);

        //Get fields to display
        $formFieldsList = $this->sellsyPlugin->listSelectedFields();
        //Get mandatories fields
        $mandatoryFieldsList = array_flip((array) $this->options[Settings::MANDATORIES_FIELDS]);
        $result = $this->validateForm($formFieldsList, $attr, $formId);

        if (is_readable(SELLSY_WP_PATH_INC.'/front-page.php')) {
            $options = $this->options;

            $messageSent = false;
            if (true === $result) {
                $messageSent = true;
            } elseif (!empty($result)) {
                $errors = $result;
            }

            include SELLSY_WP_PATH_INC.'/front-page.php';
        }
    }
}
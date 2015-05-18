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

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Front
 * Class to prepare Wordpress front end's routines to use this plugin in the public area to display
 * HTML forms and create leads
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Front
{
    /**
     * Object to manipulate to configure the plugin
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * Object to access and store all dynamics parameters neededby this plugin
     * @var OptionsBag
     */
    protected $options;

    /**
     * TO count usage of shortcode in a single request.
     *
     * @var int
     */
    protected static $formCounter = 0;

    /**
     * Constructor
     * @param Plugin     $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct($sellsyPlugin, $options)
    {
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;
    }

    /**
     * To add new css stylesheet in front when this plugin is used
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
     * Method called by the plugin to validate values submitted via a Sellsy's form by a visitor
     * If values are corrects a prospect is created
     * @param array  $selectedFields
     * @param array  $attr           of the short code
     * @param string $formId         to ignore request if this request is not destinate to this form
     * @param array  $postValues
     *
     * @return null|int id of the prospect if it was created
     */
    public function validateForm(array &$selectedFields, $attr = array(), $formId, $postValues = array())
    {
        if (empty($postValues)) {
            //Values are not fetched by the plugin, read the superglobal $_POST to get them.
            $postValues = $_POST;
        }

        if (!\is_admin() //We are in front
            && isset($postValues['send_wp_sellsy']) //The form was sent
            && isset($postValues['slswp_nonce_verify_page']) //Nonce/Xsrf is present
            && \wp_verify_nonce($postValues['slswp_nonce_verify_page'], 'slswp_nonce_field')) { //Nonce is valid

            if (!isset($postValues['formId']) || $postValues['formId'] != $formId) {
                //Not good form
                return false;
            }

            //Form's value are valid, extract value and create a prospect in Sellsy
            $postValues = array_intersect_key($postValues, $selectedFields);
            $body = null;
            $prospectReturn = $this->sellsyPlugin->createProspect($postValues, $body);

            if (is_numeric($prospectReturn)) {
                //Prospect goodly created in the Sellsy account
                if ('prospectOpportunity' == $this->options[Settings::OPPORTUNITY_CREATION]) {
                    //This plugin is configured to create also an opportunity about this prospect
                    $source = $this->extractSource($attr);
                    //So creates it
                    $this->sellsyPlugin->createOpportunity($prospectReturn, $source, '');
                }

                if (!empty($this->options[Settings::SUBMIT_NOTIFICATION]) && !empty($body)) {
                    //This plugin is configured to send a notification via email, performs it
                    $this->sellsyPlugin->sendMail($body);
                }

                return true;
            } else {
                return $prospectReturn;
            }
        }

        return false;
    }

    /**
     * Return the source to use to create opportunity.
     *
     * @param array $attr
     *
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
     * Return the form id via attribute. If there are no form id, compute it.
     *
     * @param array $attr
     *
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
     * To compute shortcode insert in a page.
     *
     * @param string     $attr
     * @param null|mixed $content
     */
    public function shortcode($attr, $content = null)
    {
        $formId = $this->getFormId($attr);

        //Get fields to display
        $formFieldsList = $this->sellsyPlugin->listSelectedFields();
        $result = $this->validateForm($formFieldsList, $attr, $formId);

        if (is_readable(SELLSY_WP_PATH_INC.'/front-page.php')) {
            $options = $this->options;

            $messageSent = false;
            if (true === $result) {
                $messageSent = true;
            } elseif (!empty($result)) {
                $errors = $result;
            }

            $opportunityId = $this->sellsyPlugin->getOpportunityCurrentIdent();

            include SELLSY_WP_PATH_INC.'/front-page.php';
        }
    }
}

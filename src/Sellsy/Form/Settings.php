<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Settings
 * Class to build setting form in WP to allow admin to manage this plugin
 * @package UniAlteri\Sellsy\Admin
 */
class Settings
{
    /**
     * List of fields in settings forms
     * @var array
     */
    protected $settings = [];

    /**
     * Section list in admin panel
     * @var array
     */
    protected $sections = [];

    /**
     * @var OptionsBag
     */
    protected $options;

    /**
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * Initialize this object
     * @param Plugin $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct(Plugin $sellsyPlugin, OptionsBag $options)
    {
        //Register the options bag
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;

        //Initialize this object
        $this->settings = $this->loadSettings();
        $this->sections = $this->loadSections();

        //If there are no registered options
        if (false == $options->isDefined()) {
            $this->initialize();
        }
    }

    /**
     * Return the list of available settings in the wordpress admin
     * @return array
     */
    public function loadSettings()
    {
        return [
            /* Section Connexion Sellsy */
            'WPIconsumer_token' => [
                'title' => __('Consumer Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion'
            ],
            'WPIconsumer_secret' => [
                'title' => __('Consumer Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion'
            ],
            'WPIutilisateur_token' => [
                'title' => __('Utilisateur Token', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion'
            ],
            'WPIutilisateur_secret' => [
                'title' => __('Utilisateur Secret', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_connexion'
            ],
            /* Section Options du plugin */
            'WPIcreer_prospopp' => [
                'title' => __('Créer', 'wpsellsy'),
                'desc' => '',
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => [
                    'choice1' => __('Un prospect seulement', 'wpsellsy'),
                    'choice2' => __('Un prospect et une opportunité', 'wpsellsy')
                ]
            ],
            'WPIenvoyer_copie' => [
                'title' => __('Envoyer une copie à', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options'
            ],
            'WPInom_opp_source' => [
                'title' => __('Nom de la source pour les opportunités', 'wpsellsy'),
                'desc' => __('Vous devez renseigner ce champ si vous souhaitez créer une opportunité en plus d\'un prospect. La source doit exister sur votre compte <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a>.' , 'wpsellsy'),
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options'
            ],
            'WPInom_form' => [
                'title' => __('Nom du formulaire', 'wpsellsy'),
                'desc' => '',
                'std' => '',
                'type' => 'text',
                'section' => 'sellsy_options'
            ],
            'WPIaff_form' => [
                'title' => __('Afficher le nom du formulaire', 'wpsellsy'),
                'desc' => __('Vous permet d\'afficher ou de masquer le nom du formulaire inclus dans vos pages et/ou articles via le shortcode.', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_options',
                'choices' => [
                    'choice1' => __('Oui', 'wpsellsy'),
                    'choice2' => __('Non', 'wpsellsy')
                ]
            ],
            /* Section Charger jQuery */
            'WPIloadjQuery' => [
                'title'   => __('Activer', 'wpsellsy'),
                'desc'    => __('Désactive la version de jQuery incluse dans votre thème (si il y en a une) et intègre la version 1.9.1 du CDN Google.', 'wpsellsy'),
                'type'    => 'radio',
                'std'     => '',
                'section' => 'sellsy_loadjQuery',
                'choices' => [
                    'choice1' => __('Oui', 'wpsellsy'),
                    'choice2' => __('Non', 'wpsellsy')
                ]
            ],
            /* Section Activer validation JS */
            'WPIjsValid' => [
                'title' => __('Activer', 'wpsellsy'),
                'desc' => __('La validation Javascript permet de vérifier les informations saisies avant que le formulaire soit soumis au serveur (sans rafraîchissement de la page).', 'wpsellsy'),
                'type' => 'radio',
                'std' => '',
                'section' => 'sellsy_jsValid',
                'choices' => [
                    'choice1' => __('Oui', 'wpsellsy'),
                    'choice2' => __('Non', 'wpsellsy')
                ]
            ],
            /* Section Champs */
            'WPIFieldsSelected' => [
                'title' => __('Activer', 'wpsellsy'),
                'desc' => __('Sélectionner les champs à afficher', 'wpsellsy'),
                'type' => 'multiselect',
                'std' => '',
                'section' => 'sellsy_Champs',
                'choices' => $this->sellsyPlugin->listCustomFields()
            ],
        ];
    }

    /**
     * Return the list of available sections in the wordpress admin
     * @return array
     */
    public function loadSections()
    {
        return [
            'sellsy_connexion'	=> __('Connexion à votre compte Sellsy', 'wpsellsy'),
            'sellsy_options' => __('Options du plugin', 'wpsellsy'),
            'sellsy_loadjQuery' => __('Charger le framework jQuery du plugin (' . SELLSY_WP_JQUERY_VERSION . ')', 'wpsellsy'),
            'sellsy_jsValid' => __('Validation Javascript (requiert jQuery)', 'wpsellsy'),
            'sellsy_Champs' => __('Sélection des champs', 'wpsellsy')
        ];
    }

    /**
     * Return sections configured by this object
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Initialize options of this plugin with default value
     * @return $this;
     */
    public function initialize()
    {
        //Set default value in options bag
        foreach ($this->settings as $id=>&$params) {
            if (isset($params['std'])) { //by pass fields without default value
                $this->options[$id] = $params['std'];
            }
        }

        //Save option bag in wordpress
        $this->options->save();

        return $this;
    }

    /**
     * Method to build form in wordpress admin to manage this plugin
     * @param callable $displaySectionCallback
     * @param callable $displayFieldsCallback
     * @return $this
     */
    public function buildForms($displaySectionCallback, $displayFieldsCallback)
    {
        if (!is_callable($displaySectionCallback) || !is_callable($displayFieldsCallback)) {
            return $this;
        }

        foreach ($this->sections as $slug=>$title) {
            \add_settings_section($slug, $title, $displaySectionCallback, 'slswp-admPage');
        }

        foreach ($this->settings as $id=>$setting) {
            $setting['id'] = $id;
            $this->createInput($setting, $displayFieldsCallback);
        }

        return $this;
    }

    /**
     * Create input from a setting in the form
     * @param array $setting
     * @param callable $displayCallback
     * @return $this
     */
    public function createInput(&$setting, $displayCallback)
    {
        $defaultsSettings = [
            'id' => 'champ_defaut',
            'title' => 'Champ par défaut',
            'desc' => __('Description par défaut', 'wpsellsy'),
            'std' => '',
            'type' => 'text',
            'section' => 'sellsy_connexion',
            'choices' => [],
            'class'	=> ''
        ];

        $setting = wp_parse_args($setting, $defaultsSettings);

        $fieldSettings = [
            'type' => $setting['type'],
            'id' => $setting['id'],
            'desc' => $setting['desc'],
            'std' => $setting['std'],
            'choices' => $setting['choices'],
            'label_for' => $setting['id'],
            'class' => $setting['class']
        ];

        add_settings_field(
            $setting['id'],
            $setting['title'],
            $displayCallback,
            'slswp-admPage',
            $setting['section'],
            $fieldSettings
        );

        return $this;
    }
}
<?php
namespace NCPC;

/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );        
        add_filter( 'upload_mimes', [$this, 'ncpc_add_custom_mime_types'] );
        add_filter( 'wp_check_filetype_and_ext', [$this, 'ncpc_check_filetype_and_ext'], 99, 5 );
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu() {
        global $submenu;

        $capability = 'manage_product_terms';
        $slug       = 'ncpc';
        $icon = NCPC_ASSETS.'/images/3.png';
        $hook = add_menu_page( __( 'NCPC', 'neon-channel-product-customizer-free'), __( 'NCPC', 'neon-channel-product-customizer-free'), $capability, $slug, [ $this, 'plugin_page' ], $icon );

        if ( current_user_can( $capability ) ) {
            $submenu[ $slug ][] = array( __( 'App', 'neon-channel-product-customizer-free'), $capability, 'admin.php?page=' . $slug . '#/global-settings' );
        }

        add_action( 'load-' . $hook, [ $this, 'init_hooks'] );
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'ncpc-runtime',NCPC_ASSETS . '/js/runtime.js',false,true,NCPC_VERSION );
        wp_enqueue_script( 'ncpc-vendor',NCPC_ASSETS . '/js/vendors.js',false,true,NCPC_VERSION );
        //styles
        wp_enqueue_style( 'ncpc-style',NCPC_ASSETS . '/css/style.css',false,NCPC_VERSION );
        wp_enqueue_style( 'ncpc-admin',NCPC_ASSETS . '/css/admin.css',false,NCPC_VERSION );
         wp_enqueue_style('ncpc-toast-css',NCPC_ASSETS.'/utilities/toast.min.css',false,NCPC_VERSION);
        //wp_enqueue_style( 'ncpc-frontend' );
         
        //scripts
        wp_enqueue_script( 'ncpc-admin',NCPC_ASSETS . '/js/admin.js',[ 'jquery', 'ncpc-vendor', 'ncpc-runtime' ],true,NCPC_VERSION );
        wp_enqueue_script('ncpc-toast-js',NCPC_ASSETS.'/utilities/toast.min.js',false,true,NCPC_VERSION);
        wp_enqueue_script('ncpc-sortable',NCPC_ASSETS.'/utilities/sortable.js',false,true,NCPC_VERSION);
        wp_enqueue_media();
       // wp_localize_script( 'ncpc-admin', 'ncpcText', array('getVueTranslation' => 'get_vue_translation') );
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page() {
        ob_start();
        $this->register_enqueue_script();
        ?>
        <div class='wrap'>
            <div id='ncpc-admin-app'></div>
        </div>
        <?php
        echo wp_kses_post(ob_get_clean());
    }

    private function register_enqueue_script () {
        wp_localize_script("ncpc-admin","ncpcData",[
            "version"=>NCPC_VERSION,
            "ncpc_rest_url"=> get_rest_url().'ncpc/v1',
            "ncpc_translate_text"=>$this->getTranslateData(),
            "ncpc_assets_url"=>NCPC_ASSETS,
            'currencySymbol'     => class_exists( 'WooCommerce' ) ? html_entity_decode(get_woocommerce_currency_symbol()) : '',
            'currency_pos'       => class_exists( 'WooCommerce' ) ? get_option('woocommerce_currency_pos') : ''
        ]);
    }

    /**
     * 
     */
    private function getTranslateData() {
        return [
            "save"=> __("Save","neon-channel-product-customizer-starter"),
            "default"=>__("Default","neon-channel-product-customizer-starter"),
            "delete"=> __("Delete","neon-channel-product-customizer-starter"),
            "edit"=> __("Edit","neon-channel-product-customizer-starter"),
            "duplicate"=> __("Duplicate","neon-channel-product-customizer-starter"),
            "preview"=>__("Preview","neon-channel-product-customizer-starter"),
            "back"=>__("Back","neon-channel-product-customizer-starter"),
            "reset"=>__("Reset All","neon-channel-product-customizer-starter"),
            "emptyLabel"=>__("The label must not be empty","neon-channel-product-customizer-starter"),
            "emptyUrl"=>__("The url must not be empty","neon-channel-product-customizer-starter"),
            "image"=>[
                "title"=>"Images",
                "button"=>__("choose a picture","neon-channel-product-customizer-starter"),
                "buttonIcon"=>__("choose a icon","neon-channel-product-customizer-starter"),
                "prevImage"=>[
                    "label"=>__("Preview Image (optional) ","neon-channel-product-customizer-starter"),
                    "description"=>__("Upload an image to display the color or gradient. Adding an image will replace the preview color.","neon-channel-product-customizer-starter"),
                ],
                "popupImage"=>[
                    "label"=>__("Popup Image (optional)","neon-channel-product-customizer-starter"),
                    "description"=>__("Example Image - displayed as popup","neon-channel-product-customizer-starter"),
                ]
            ],
            "additionnalPricing"=>[
                "title"=>__("Pricing (optional) ","neon-channel-product-customizer-starter"),
                "none"=>__("None ","neon-channel-product-customizer-starter"),
                "basePrice"=>__("Base Price","neon-channel-product-customizer-starter"),
                "priceMultiplier"=>__("Price Multiplier","neon-channel-product-customizer-starter"),
                "descriptionBase"=>__("Additional cost when selected by customer (e.g. $10.00).","neon-channel-product-customizer-starter"), 
                "descriptionMultiplier"=>__("Multiply the final price of the sign when selected (e.g. 2 x 100).","neon-channel-product-customizer-starter"), 
            ],
            "menu"=>__("MENU","neon-channel-product-customizer-starter"),
            "defaultHeaderTitle"=>__("Manage your sign configurations","neon-channel-product-customizer-starter"),
            "getHelp"=>__("Get Help","neon-channel-product-customizer-starter"),
            "configuration"=> [
                "title"=>__("Configuration","neon-channel-product-customizer-starter"),
                "modal"=>[
                    "edit"=>__("Edit the name of sign configuration","neon-channel-product-customizer-starter"),
                    "duplicate"=>__("Enter a new name for the duplicated configuration","neon-channel-product-customizer-starter"),
                    "delete"=>__("Do you want to remove this configuration?","neon-channel-product-customizer-starter"),
                ],
                "header"=>[
                    "title"=>__("Configurations","neon-channel-product-customizer-starter"),
                    "all"=>__("All","neon-channel-product-customizer-starter"),
                    "name"=>__("Name","neon-channel-product-customizer-starter"),
                    "customId"=>__("Customiser ID","neon-channel-product-customizer-starter"),
                    "model"=>__("Model","neon-channel-product-customizer-starter"),
                    "productType"=>__("Product Type","neon-channel-product-customizer-starter"),
                    "action"=>__("Actions","neon-channel-product-customizer-starter"),
                ],
                "empty"=>__("No configurations currently added","neon-channel-product-customizer-starter"),
                "addNew" => __("Add new configuration","neon-channel-product-customizer-starter"),
                "start"=>[
                    "step"=>__("Step","neon-channel-product-customizer-starter"),
                    "state1"=>[
                        "header"=>__("Create your new sign configuration","neon-channel-product-customizer-starter"),
                        "description"=>__("Follow these steps to setup a new sign configuration.","neon-channel-product-customizer-starter"),
                        "label"=> __("Enter a name for your new sign configuration","neon-channel-product-customizer-starter"),
                    ],
                    "state2"=>[
                        "header"=>__("Choose your product type","neon-channel-product-customizer-starter"),
                        "description"=>__("Each product type has a unique set of configurable options.","neon-channel-product-customizer-starter"),
                    ],
                    "state3"=>[
                        "header"=>__("Choose a pricing model","neon-channel-product-customizer-starter"),
                        "description"=>__("Select the pricing and sizing model for your new sign configuration.","neon-channel-product-customizer-starter"),
                    ],
                    "state4"=>[
                        "header"=>__("Include demo data?","neon-channel-product-customizer-starter"),
                        "description"=>__("To help you get started we can automatically add fonts, colors, prices and sizes to your new configuration.","neon-channel-product-customizer-starter"),
                        "modal"=>[
                            "header"=> __("Confirm saving sign configuration","neon-channel-product-customizer-starter"),
                            "title"=> __("You have selected the following options for your new sign configuration:","neon-channel-product-customizer-starter"),
                            "option1"=> __("Product type","neon-channel-product-customizer-starter"),
                            "option2"=> __("Letter model","neon-channel-product-customizer-starter"),
                            "option3"=> __("Include demo data","neon-channel-product-customizer-starter"),
                            "description"=> __('Once you click "Save and Continue" you will <b>not be able to change the letter model or product type</b> for this sign configuration again.',"neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "type"=>[
                        "neon"=>[
                            "header"=> __("Neon signs","neon-channel-product-customizer-starter"),
                            "title"=> __("Best for flex LED / glass neon letter signs","neon-channel-product-customizer-starter"),
                            "description"=> __("Create illuminated signs in one or more colors with efficient pricing systems on all customization options","neon-channel-product-customizer-starter"),
                        ],
                        "channel"=>[
                            "header"=> __("Channel Signs","neon-channel-product-customizer-starter"),
                            "title"=> __("Best for 2D non-lit & 3D illuminated letter signs","neon-channel-product-customizer-starter"),
                            "description"=> __("Create signs of all shapes and sizes with efficient pricing systems on all customization options","neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "mode"=>[
                        "simple"=>[
                            "header"=> __("Simple","neon-channel-product-customizer-starter"),
                            "description"=> __("This model is recommended if you're new to the signage industry.","neon-channel-product-customizer-starter"),
                            "options"=> [
                                __("Fixed price per letter","neon-channel-product-customizer-starter"),
                                __("Fixed price per line","neon-channel-product-customizer-starter"),
                                __("Determine sign size by preset width and approximate height","neon-channel-product-customizer-starter"),
                                __("Accurate sizing and measurements by Approximate to selected width","neon-channel-product-customizer-starter"),
                                __("Maximum of 3 lines of text per size","neon-channel-product-customizer-starter"),
                            ],
                        ],
                        "advanced"=>[
                            "header"=> __("Advanced","neon-channel-product-customizer-starter"),
                            "description"=> __("Ensure accurate pricing and sizing for your signs while creating the best experience for your customers.","neon-channel-product-customizer-starter"),
                            "options"=> [
                                __("Fixed price per letter","neon-channel-product-customizer-starter"),
                                __("Material length pricing","neon-channel-product-customizer-starter"),
                                __("Shipping size calculator","neon-channel-product-customizer-starter"),
                                __("Accurate sizing and measurements auto","neon-channel-product-customizer-starter"),
                                __("Let customers choose their own size","neon-channel-product-customizer-starter"),
                                __("Min height for lowercase & uppercase letters","neon-channel-product-customizer-starter"),
                            ],
                        ]
                    ],
                    "demoData"=>[
                        "header"=> __("Include demo data?","neon-channel-product-customizer-starter"),
                        "description"=> __("To help you get started we can automatically add fonts, colors, prices and sizes to your new configuration","neon-channel-product-customizer-starter"),
                        "includeButton"=> __("Include Demo Data","neon-channel-product-customizer-starter"),
                        "noIncludeButton"=> __("No Include Demo Data","neon-channel-product-customizer-starter"),
                        "modal"=>[
                            "header"=> __("Confirm saving sign configuration","neon-channel-product-customizer-starter"),
                            "title"=> __("You have selected the following options for your new sign configuration:","neon-channel-product-customizer-starter"),
                            "type"=> __("Product type","neon-channel-product-customizer-starter"),
                            "mode"=> __("Letter model","neon-channel-product-customizer-starter"),
                            "include"=> __("Include demo data","neon-channel-product-customizer-starter"),
                            "description"=> __('Once you click "Save and Continue" you will not be able to change the letter model or product type for this sign configuration again.',"neon-channel-product-customizer-starter"),
                            "cancel"=> __("Cancel","neon-channel-product-customizer-starter"),
                            "save"=> __("Save And Continue","neon-channel-product-customizer-starter"),
                        ]

                    ]
                ],
                "requiredOptions"=> [
                    "title"=>__("Required options","neon-channel-product-customizer-starter"), 
                    "sizeOptions"=> [
                        "tabHeader"=>__("Sizes","neon-channel-product-customizer-starter"),
                        "newSize"=>__("Add new size","neon-channel-product-customizer-starter"),
                        "sizeItem"=> [
                            "modeSimple" =>[
                                "resize"=> [
                                    "title"=> __("Resize Settings","neon-channel-product-customizer-starter"),
                                    "enableAutoResize"=> __("Enable auto resize","neon-channel-product-customizer-starter"),
                                    "desktop" => __("On desktop","neon-channel-product-customizer-starter"),
                                    "mobile" => __("On mobile","neon-channel-product-customizer-starter"),
                                    "minFontSize"=>[
                                        "label"=>__("Min Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("Font size to which we no longer reduce","neon-channel-product-customizer-starter")
                                    ],
                                    "defaultFontSize"=>[
                                        "label"=>__("Default Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("Default font size","neon-channel-product-customizer-starter")
                                    ],
                                    "maxFontSize"=>[
                                        "label"=>__("Max Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("Largest value the font size can take","neon-channel-product-customizer-starter")
                                    ],
                                ],
                                "sizeLabel"=> [
                                    "label"=>__("Label","neon-channel-product-customizer-starter"),
                                    "description"=>__("The name of the size (e.g. small)","neon-channel-product-customizer-starter"),
                                ],
                                "sizeDescription"=> [
                                    "label"=>__("Description","neon-channel-product-customizer-starter"), 
                                    "description"=>__("e.g. Maximum 60cm (2ft) in length.","neon-channel-product-customizer-starter") 
                                ],
                                "sizeLine"=> [
                                    "sizeWidth"=> [
                                    "title"=>__("Size Width And Lines Of Text","neon-channel-product-customizer-starter"), 
                                        "width"=>[
                                        "label"=>__("Width(centimeters)","neon-channel-product-customizer-starter"),
                                        "description"=>__("This sign size will always be the width defined with this input.","neon-channel-product-customizer-starter"),
                                        ],
                                        "maxLines"=> [
                                        "label"=>__("Maximum lines of text for this size","neon-channel-product-customizer-starter"),
                                        "description"=>__("The number of lines of text that is allowed for this size.","neon-channel-product-customizer-starter")
                                        ]
                                    ], 
                                ],   
                                "sizeCharacters"=>[
                                    "text"=>__("Minimum and Maximum characters allowed for this size","neon-channel-product-customizer-starter"),
                                    "minChar"=> [
                                    "label"=>__("Minimum Text Characters","neon-channel-product-customizer-starter"),
                                    "description"=>__("The minimum number of text characters for the entire sign. The default minimum is 3 characters.","neon-channel-product-customizer-starter"),
                                    ],
                                    "maxChar"=>[
                                    "label"=>__("Maximum Text Characters per line","neon-channel-product-customizer-starter"),
                                    "description"=>__("The maximum number of text characters allowed per line (e.g. 20 text characters per line).","neon-channel-product-customizer-starter"),
                                    ]
                                ],
                            ],
                            "modeAdvanced" =>[
                                "sizeLabel"=>
                                [
                                    "label"=>__("Label","neon-channel-product-customizer-starter"),
                                    "description"=>__("The name of the size (e.g. small)","neon-channel-product-customizer-starter"),
                                ],
                                "resize"=> [
                                    "title"=> __("Resize Settings","neon-channel-product-customizer-starter"),
                                    "enableAutoResize"=> __("Enable auto resize","neon-channel-product-customizer-starter"),
                                    "desktop" => __("On desktop","neon-channel-product-customizer-starter"),
                                    "mobile" => __("On mobile","neon-channel-product-customizer-starter"),
                                    "minFontSize"=>[
                                        "label"=>__("Min Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("The font size at which we no longer reduce","neon-channel-product-customizer-starter")
                                    ],
                                    "defaultFontSize"=>[
                                        "label"=>__("Default Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("The default font size","neon-channel-product-customizer-starter")
                                    ],
                                    "maxFontSize"=>[
                                        "label"=>__("Max Font Size","neon-channel-product-customizer-starter"),
                                        "description"=>__("The largest value the font size can take","neon-channel-product-customizer-starter")
                                    ],
                                ],
                                "scale"=>
                                [
                                    "label"=>__("Text Scale","neon-channel-product-customizer-starter"), 
                                    "description"=>__("Set a multiplier for the overall size of the sign size (e.g. set 1 for a 100% of the signs size, 2 for 200% of the signs size).","neon-channel-product-customizer-starter") 
                                ],
                            ], 
                        ], 
                        "listSizes"=>[
                            "modeSimple"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                                "width"=>__("Width","neon-channel-product-customizer-starter"),
                                "minChar"=>__("Min char","neon-channel-product-customizer-starter"),
                                "maxChar"=>__("Max char","neon-channel-product-customizer-starter"),
                                "lineLimit"=>__("Line limite","neon-channel-product-customizer-starter"),
                                "action"=>__("Actions","neon-channel-product-customizer-starter"),
                            ],
                            "modeAdvanced"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                                "sizeScaleMultiplier"=>__("Size Scale Multiplier","neon-channel-product-customizer-starter"),
                                "action"=>__("Actions","neon-channel-product-customizer-starter"),
                            ],
                        ],
                        "sizeSetting"=>[
                            "modeSimple"=>[
                                "label"=>__("Size Settings","neon-channel-product-customizer-starter"),
                                "title"=>__("Title","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"),
                                "autoSwitching"=>
                                [
                                    "label"=>__("Prevent size automatically switching up","neon-channel-product-customizer-starter"),
                                    "description"=>__("When the amount of characters per line entered by the customer exceeds the maximum number of characters per line for the current size, the app will automatically switch to the next size with a higher character limit per line. Check this option to stop this behaviour.","neon-channel-product-customizer-starter")
                                ],
                            ],
                            "modeAdvanced"=>[
                                "label"=>__("Size Settings","neon-channel-product-customizer-starter"),
                                "title"=>__("Title","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"),
                                "character"=> [
                                    "Label"=>__("CHARACTER AND SIGN DIMENSION LIMITS","neon-channel-product-customizer-starter"),
                                    "description"=>__("Description","neon-channel-product-customizer-starter"),
                                    "title"=>__("Minimum Text Characters","neon-channel-product-customizer-starter"),
                                    "explain"=>__("The minimum number of text characters for the sign.","neon-channel-product-customizer-starter"),
                                    
                                ],
                                "maxWidth"=>[
                                    "title"=>__("Maximum Sign Width","neon-channel-product-customizer-starter"),
                                    "Unlimited"=>[
                                        "enable"=>[
                                            "title"=>__("Unlimited Width","neon-channel-product-customizer-starter"),   
                                        ],
                                    ],
                                    "limited"=>[
                                        "enable"=>[
                                            "title"=>__("Limited width","neon-channel-product-customizer-starter"),
                                            "explain"=>__("Set a maximum width for the sign in centimeters. A popup will appear if the sign exceeds this width.","neon-channel-product-customizer-starter"),  
                                        ],
                                    ],
                                ],
                                "maxheight"=>[
                                    "title"=>__("Maximum Sign height","neon-channel-product-customizer-starter"),
                                    "Unlimited"=>[
                                        "enable"=>[
                                            "title"=>__("Unlimited height","neon-channel-product-customizer-starter"),   
                                        ],
                                    ],
                                    "limited"=>[
                                        "enable"=>[
                                            "title"=>__("Limited height","neon-channel-product-customizer-starter"),
                                            "explain"=>__("Set a maximum height for the sign in centimeters. A popup will appear if the sign exceeds this height.","neon-channel-product-customizer-starter"),  
                                        ],
                                    ],
                                ],
                                "Custom"=>[
                                    "title"=>__("CUSTOM SIZING","neon-channel-product-customizer-starter"),
                                    "description"=>__("Show an additional option in sizes that will allow the customer to change the width of sign using a range slider.","neon-channel-product-customizer-starter"),
                                    "Disable"=>[
                                        "enable"=>[
                                            "title"=>__("Disable Custom Sizing","neon-channel-product-customizer-starter"),   
                                        ],
                                    ],
                                    "enableCustom"=>[
                                        "enable"=>[
                                            "label"=>__("Enable Custom Sizing","neon-channel-product-customizer-starter"),
                                            "explain"=>__("Customers can set a custom size for their sign.","neon-channel-product-customizer-starter"),
                                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                                            "titleExplain"=>__("Maximum Range in centimeters","neon-channel-product-customizer-starter"),
                                            "max"=>__("Maximum Range in centimeters","neon-channel-product-customizer-starter"),
                                            "maxExplain"=>__("Maximum width that the range slider will allow. If left blank, it will default to 300cm.","neon-channel-product-customizer-starter"),
                                            "step"=>__("Step","neon-channel-product-customizer-starter"),
                                            "stepExplain"=>__("The step of evolution of the custom line.","neon-channel-product-customizer-starter"),
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this size ?","neon-channel-product-customizer-starter"),
                        ],
                    ],
                    "pricingOption"=>[
                        "tabHeader"=>__("Pricing","neon-channel-product-customizer-starter"),
                        "newPricing"=>__("Add new pricing","neon-channel-product-customizer-starter"),
                        "modal"=>[
                            "delete"=>__("Do you want to remove this pricing ?","neon-channel-product-customizer-starter"),
                        ],
                        "pricingsItem"=>[
                            "pricingsLabel"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                                "description"=>__("The label is not shown to the customer.(Required)","neon-channel-product-customizer-starter"),
                            ],
                            "modeSimple"=>[
                                "pricingsForSize"=>[
                                    "title"=>__("Pricing for each size","neon-channel-product-customizer-starter"),
                                    "description"=>__("Width (centimeters) Maximum lines of text for this size This sign size will always be the width defined with this input. The number of lines of text that is allowed for this size.","neon-channel-product-customizer-starter"),
                                    "moreSize"=>__("Add more size","neon-channel-product-customizer-starter"),
                                ],
                                "sizes"=>[
                                    "label"=>__("Size: ","neon-channel-product-customizer-starter"),
                                    "line"=>[
                                        "line"=>__("Line","neon-channel-product-customizer-starter"),
                                        "basePrice"=>__("Base price","neon-channel-product-customizer-starter"),
                                        "letterPrice"=>__("Letter price","neon-channel-product-customizer-starter"),
                                        "nbCharStartAddPricePerLetter"=>__("Number of characters in Base Price","neon-channel-product-customizer-starter")
                                    ],  
                                ],
                            ],
                            "modeAdvanced"=>[
                                "pricingMethod" =>__("Letter Pricing Method","neon-channel-product-customizer-starter"),
                                "fixed"=>[
                                    "title"=>__("Fixed cost per letter","neon-channel-product-customizer-starter"),
                                ],
                                "letter"=>[
                                    "title"=>__("Letter material cost","neon-channel-product-customizer-starter"),
                                ],
                                "both"=>[
                                    "title"=>__("Letter pricing method And letter material cost","neon-channel-product-customizer-starter"),
                                ],
                                "Price"=>[
                                    "formula"=>__("Price Formula","neon-channel-product-customizer-starter"),
                                    "description"=>__("Add additional size ranges to apply unique price calculations for a given size range.d","neon-channel-product-customizer-starter"),
                                ],
                                "sizes"=>[
                                    "label"=>__("Size Range 1 - Starts at 0 - continue to infinity","neon-channel-product-customizer-starter"),
                                    "max"=>[
                                        "title"=>__("Max Size Range","neon-channel-product-customizer-starter"),
                                        "description"=>__("Define the maximum width and height range that the below price inputs will apply too","neon-channel-product-customizer-starter"),
                                    ],
                                    "shipping"=>[
                                        "label"=>__("Shipping Price","neon-channel-product-customizer-starter"),
                                        
                                        "description"=>__("Dynamic price based on final sign dimensions.","neon-channel-product-customizer-starter"),
                                    ],
                                    "easily"=>[
                                        "title"=>__("Easily convert shipping price","neon-channel-product-customizer-starter"),
                                        "price"=>[
                                            "title1"=>__("Price Per Letter","neon-channel-product-customizer-starter"),
                                            "title2"=>__("Letter Material Cost","neon-channel-product-customizer-starter"),
                                            "description1"=>__("Cost of each letter for this size range.","neon-channel-product-customizer-starter"),
                                            "description2"=>__("Cost of the material used to create the letters for this sign (e.g. flexible neon, glass).","neon-channel-product-customizer-starter"),
                                        ], 
                                        "Start"=>[
                                            "title"=>__("Start Price - (Optional)","neon-channel-product-customizer-starter"),
                                            "description"=>__("Included any additional cost (e.g. building cost).","neon-channel-product-customizer-starter"),
                                        ] 
                                    ],   
                                ],
                            ]
                        ],
                        "listPricing"=>[
                            "modeSimple"=>[
                                "name"=>__("Name","neon-channel-product-customizer-starter"),
                                "action"=>__("Actions","neon-channel-product-customizer-starter"),
                            ],
                            "modeAdvanced"=>[
                                "name"=>__("Name","neon-channel-product-customizer-starter"),
                                "letter"=>__("Letter price Method","neon-channel-product-customizer-starter"),
                                "action"=>__("Actions","neon-channel-product-customizer-starter"),  
                            ]
                        ]
                    ],
                    "fontOptions"=> [
                        "tabHeader"=>__("Fonts","neon-channel-product-customizer-starter"),
                        "newFont"=>__("Add new font","neon-channel-product-customizer-starter"),
                        "pricing"=>__("Pricing","neon-channel-product-customizer-starter"),
                        "typeFont"=>[
                            "googleFont"=>[
                                "header"=>__("Google Font (default)","neon-channel-product-customizer-starter"),
                                "font"=>[
                                    "chooseFont"=>__("Choose google fonts","neon-channel-product-customizer-starter"),
                                    "label"=>__("Label","neon-channel-product-customizer-starter"),
                                    "description"=>__("The name of the font (e.g. Roboto).","neon-channel-product-customizer-starter"),
                                ], 
                                "variant"=>__("Choose font variant(Required)","neon-channel-product-customizer-free"),                   
                            ],
                            "upload"=>[
                                "header"=>__("Upload","neon-channel-product-customizer-starter"),
                                "font"=>[
                                    "title"=>__("Upload font","neon-channel-product-customizer-starter"), 
                                    "label"=>__(".ttf Font File Type (Required)","neon-channel-product-customizer-starter"), 
                                    "choose"=>[
                                        "label"=>__("Choose a font file","neon-channel-product-customizer-starter"), 
                                        "description"=>__("For best results keep the label of the font to one word only as this label is shown to the customer.","neon-channel-product-customizer-starter"), 
                                    ],
                                ]
                            ],
                            "fontName"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"), 
                                "description"=>__("The name of the font (e.g. Roboto).","neon-channel-product-customizer-starter"),
                            ],
                            "modeSimple"=>[
                                "pricing"=>__("Pricing","neon-channel-product-customizer-starter"),
                                "limit"=>__("Limit this font to a size and above (optional)","neon-channel-product-customizer-starter"),  
                                "description"=>__("Every font belongs to a simple pricing model.","neon-channel-product-customizer-starter"),
                            ],
                            "modeAdvanced"=>[
                                "pricing"=>__("Pricing","neon-channel-product-customizer-starter"),
                                "smallest"=>[
                                    "label"=>__("Minimum height for smallest letter","neon-channel-product-customizer-starter"),
                                    "description"=>__("The minimum height for smallest letter in centimeter","neon-channel-product-customizer-starter"), 
                                ],
                                "uppercase"=>[
                                    "label"=>__("Minimum height for smallest letter","neon-channel-product-customizer-starter"),
                                    "description"=>__("The minimum height for uppercase letter in centimeter","neon-channel-product-customizer-starter"), 
                                ]
                            ],
                            "style"=>[
                                "fontStyle"=>__("Font Style","neon-channel-product-customizer-starter"),
                                "lineHeight"=>__("Line Height","neon-channel-product-customizer-starter"),
                                "height"=>[
                                    "normal"=>[
                                        "label"=>__("Normal(default)","neon-channel-product-customizer-starter"),
                                    ],
                                    "specific"=>[
                                        "label"=>__("Specific Line Height","neon-channel-product-customizer-starter"),
                                        "description"=>__("Adjust the space between each new line of text for this font in the configurator","neon-channel-product-customizer-starter"),
                                        "description1"=>__("Adjust the space between each new line of text for this font in the height calculation.( just for advanced config)","neon-channel-product-customizer-starter"),
                                    ],
                                ]
        
                            ]
        
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this font ?","neon-channel-product-customizer-starter"),
                        ],
                        "listFonts"=>[
                            "font"=>__("Font","neon-channel-product-customizer-starter"),
                            "lineHeight"=>__("Line Height","neon-channel-product-customizer-starter"),
                            "pricing"=>__("Pricing","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),   
                        ],
                        "fontsSetting"=>[
                            "label"=>__("Fonts Settings","neon-channel-product-customizer-starter"),
                            "Setting"=>[
                                "title"=>__("Title","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"),
                                "explain"=>__("This will display under the title.","neon-channel-product-customizer-starter"),
                            ]
                        
                        ]
        
                    ],
                    "colorOptions"=> [
                        "tabHeader"=>__("Colors","neon-channel-product-customizer-starter"),
                        "newColor"=>__("Add new color","neon-channel-product-customizer-starter"),
                        "colorItem"=>[
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "description"=>__("The name of the color (e.g. Black).","neon-channel-product-customizer-starter"),
                            "type"=>__("Color type","neon-channel-product-customizer-starter"),
                            "color"=>__("Color ","neon-channel-product-customizer-starter"),
                            "size"=>[
                                "title"=>__("Minimum Size (optional)","neon-channel-product-customizer-starter"),
                                "minWidth"=>__("Minimum Width","neon-channel-product-customizer-starter"),
                                "explainWidth"=>__("If value is greater than 0, then this color may be disabled when the width of the sign is less than this value.","neon-channel-product-customizer-starter"),
                                "minHeight"=>__("Minimum Height ","neon-channel-product-customizer-starter"),  
                                "explainHeight"=>__("If value is greater than 0, then this color may be disabled when the height of the sign is less than this value.","neon-channel-product-customizer-starter"),
                            ],
                            "rule"=>[
                                "label"=>__("Rule","neon-channel-product-customizer-starter"),
                                "one"=>__("Disable until one of the minimum values is reached. ","neon-channel-product-customizer-starter"),
                                "both"=>__("Disable until both minimum values are reached","neon-channel-product-customizer-starter"),
                            ],
                        ],
                        "colorList"=>[
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "colorHex"=>__("Color hexcode","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),            
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this color ?","neon-channel-product-customizer-starter"),
                        ],
                        "colorSetting"=>[
                            "settingLabel"=>__("Colors Settings","neon-channel-product-customizer-starter"),
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("Provide further details about your colors (e.g. 'red and green + $10').","neon-channel-product-customizer-starter"),
                            "glowEffect"=>__("Glow Effect","neon-channel-product-customizer-starter")
                        ]  
                    ],
                    "letterTypeOptions"=> [
                        "tabHeader"=>__("Letter type","neon-channel-product-customizer-starter"),
                        "newtype"=>__("Add new letter type","neon-channel-product-customizer-starter"),
                        "typeItem"=>[
                            "example"=>__("Example","neon-channel-product-customizer-starter"),
                            "action"=>__("Action ","neon-channel-product-customizer-starter"),
                            "noLetter"=>__("No letter type currently added","neon-channel-product-customizer-starter"),
                            "listLetterType"=>[
                                "Exemple"=>__("Exemple","neon-channel-product-customizer-starter"),
                                "Label"=>__("Label","neon-channel-product-customizer-starter"),
                                "Type"=>__("Type","neon-channel-product-customizer-starter"), 
                                "Textures"=>__("Textures","neon-channel-product-customizer-starter"),
                                "Active"=>__("Active","neon-channel-product-customizer-starter"),
                                "Action"=>__("Actions","neon-channel-product-customizer-starter"),
                            ],
                            "modal"=>[
                                "delete"=>__("Do you want to remove this letter type ?","neon-channel-product-customizer-starter"),
                            ],
                            "newItem"=>[
                                "PartSetting"=>[
                                    "header"=>__("Settings","neon-channel-product-customizer-starter"),
                                    "title"=>[
                                        "label"=>__("Title","neon-channel-product-customizer-starter"),
                                    ],
                                    "description"=>[
                                        "label"=>__("Description","neon-channel-product-customizer-starter"),
                                        "description"=>__('Provide further details about the colours e.g. "red and green + $10"',"neon-channel-product-customizer-starter"),
                                    ]
                                ],
                                "texture"=>[
                                    "newTexture"=>[
                                        "addNewTexture"=>__("Add new texture","neon-channel-product-customizer-starter"),
                                        "title"=>__("Title","neon-channel-product-customizer-starter"),
                                        "color"=>__("Color","neon-channel-product-customizer-starter"),
                                        "visualEffect"=>[
                                            "title"=>__("Visual Effect","neon-channel-product-customizer-starter"),
                                            "description"=>__("Choose the visual effect for this letter part texture.","neon-channel-product-customizer-starter"),  
                                        ],
                                        "size"=>[
                                            "title"=>__("Minimum Size (optional)","neon-channel-product-customizer-starter"),
                                            "minWidth"=>__("Minimum Width","neon-channel-product-customizer-starter"),
                                            "explainWidth"=>__("When the value is greater than 0, then the selected  color will be disabled and the width of the sign will be less than the value you put.","neon-channel-product-customizer-starter"),
                                            "minHeight"=>__("Minimum Height ","neon-channel-product-customizer-starter"),  
                                            "explainHeight"=>__("When the value is greater than 0, then the selected  color will be disabled and the height of the sign will be less than the value you put.","neon-channel-product-customizer-starter"),
                                        ],
                                        "rule"=>[
                                            "label"=>__("Rule","neon-channel-product-customizer-starter"),
                                            "one"=>__("Disable until one of the minimum values is reached. ","neon-channel-product-customizer-starter"),
                                            "both"=>__("Disable until both minimum values are reached","neon-channel-product-customizer-starter"),
                                        ],
                                    ],
                                    "modal"=>[
                                        "delete"=>__("Do you want to remove this color ?","neon-channel-product-customizer-starter"),
                                    ],
                                    "listTexture"=>[
                                        "noTexture"=>__("No texture added","neon-channel-product-customizer-starter"),
                                        "label"=>__("Label","neon-channel-product-customizer-starter"),
                                        "codeHex"=>__("Color Hexacode","neon-channel-product-customizer-starter"),
                                        "visualEffect"=>__("Visual effect","neon-channel-product-customizer-starter"),
                                        "action"=>__("Actions","neon-channel-product-customizer-starter"),  
                                    ],
                                    "textureSettings"=>[
                                        "label"=>__("Face Settings","neon-channel-product-customizer-starter"),
                                        "settings"=>[
                                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                                            "explain"=>__("Provide further details about the colours e.g. red and green + $10","neon-channel-product-customizer-starter"),  
                                        ]
                                    ]
                                ]
                               
                            ],
                            "letterTypeSetting"=>[
                                "letter"=>__("Letter Types Settings","neon-channel-product-customizer-starter"),
                                "title"=>__("Title","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"), 
                                "explain"=>__("This field will be displayed under the label","neon-channel-product-customizer-starter"),
                            ]
                        
                        ] 
        
                    ]
                ],
                "additionalOption"=>[
                    "title"=>__("Additional Options","neon-channel-product-customizer-starter"), 
                    "materialOptions"=> [
                        "tabHeader"=>__("Materials","neon-channel-product-customizer-starter"),
                        "newMaterial"=>__("Add new Materiel","neon-channel-product-customizer-starter"),
                        "materialItem"=>[
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                        ],
                        "listMaterial"=>
                        [
                            "noItem"=>__("No Materials found","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "popupImage"=>__("Popup image","neon-channel-product-customizer-starter"),
                            "additionalPricing"=>__("Additional pricing","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this material ?","neon-channel-product-customizer-starter"),
                        ],
                        "materialSetting"=>
                        [
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("This will display under the title.","neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "jacketOptions"=> [
                        "tabHeader"=>__("Jackets","neon-channel-product-customizer-starter"),
                        "newJacket"=>__("Add new jacket","neon-channel-product-customizer-starter"),
                        "jacketItem"=>[
                            "item"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"),
                            ],
                            "type"=>[
                                "typeJacket"=>__("Type of jacket","neon-channel-product-customizer-starter"),
                                "description"=>__("The effect will apply to the visualiser when the sign light is turned off.","neon-channel-product-customizer-starter"), 
                            ],
                            "color"=>[
                                "colorExclusions"=>__("Color Exclusions","neon-channel-product-customizer-starter"),
                                "description"=>__("Select the colours that this jacket option will NOT be available for.","neon-channel-product-customizer-starter"), 
                            ]
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this jacket ?","neon-channel-product-customizer-starter"),
                        ],
                        "listJacket"=>
                        [
                            "noItem"=>__("No Jackets found","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "jacketSetting"=>
                        [
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("This will display under the title.","neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "backboardOptions"=> [
                        "tabHeader"=>__("Backboard","neon-channel-product-customizer-starter"),
                        "newBackboard"=>__("Add new backboard","neon-channel-product-customizer-starter"),
                        "backboardItem"=>[
                            "item"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                                "description"=>__("Description","neon-channel-product-customizer-starter"),
                            ],
                            "sign"=>[
                                "signBackboard"=>__("Sign backboard visualization","neon-channel-product-customizer-starter"),
                                "typeBackboard"=>__("Backboard type - Select how your backboard will display on the customer facing visualizer.","neon-channel-product-customizer-starter"), 
                            ],
                            "color"=>[
                                "colorExclusions"=>__("colorExclusions","neon-channel-product-customizer-starter"),
                                "description"=>__("Select the colours that this jacket option will NOT be available for.","neon-channel-product-customizer-starter"), 
                            ]
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this backboard ?","neon-channel-product-customizer-starter"),
                        ],
                        "listbackboard"=>
                        [
                            "noItem"=>__("No backboards currently added","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "backboardSetting"=>
                        [
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("Provide further details about your sizes.","neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "backboardColorOptions"=> [
                        "tabHeader"=>__("Backboard Colors","neon-channel-product-customizer-starter"),
                        "newBackboardColor"=>__("Add new backboard color","neon-channel-product-customizer-starter"),
                        "backboardColorItem"=>[
                            "item"=>[
                                "label"=>__("Label","neon-channel-product-customizer-starter"),
                            ],
                            "backboardColor"=>__("Backboard Color","neon-channel-product-customizer-starter"),
                            "sign"=>[
                                "backboardColor"=>__("Sign backboard visualization","neon-channel-product-customizer-starter"),
                            ],
                            "size"=>[
                                "title"=>__("Minimum Size (optional)","neon-channel-product-customizer-starter"),
                                "minWidth"=>__("Minimum Width","neon-channel-product-customizer-starter"),
                                "explainWidth"=>__("If value is greater than 0, then this color may be disabled when the width of the sign is less than this value.","neon-channel-product-customizer-starter"),
                                "minHeight"=>__("Minimum Height ","neon-channel-product-customizer-starter"),  
                                "explainHeight"=>__("If value is greater than 0, then this color may be disabled when the height of the sign is less than this value.","neon-channel-product-customizer-starter"),
                            ],
                            "rule"=>[
                                "title" => __("Rule","neon-channel-product-customizer-starter"),
                                "one"=>__("Disable until one of the minimum values is reached. ","neon-channel-product-customizer-starter"),
                                "both"=>__("Disable until both minimum values are reached","neon-channel-product-customizer-starter"),
                            ],
                            "exclusion"=>[
                                "colorExclusions"=>__("colorExclusions","neon-channel-product-customizer-starter"),
                                "description"=>__("Select the colours that this jacket option will NOT be available for.","neon-channel-product-customizer-starter"), 
                            ]
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this backboard color ?","neon-channel-product-customizer-starter"),
                        ],
                        "listBackboardColor"=>
                        [
                            "noItem"=>__("No backboard colors currently added","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "popupImage"=>__("Popup image","neon-channel-product-customizer-starter"),
                            "additionalPricing"=>__("Additional pricing","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "backboardColorSetting"=>
                        [
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("Provide further details about your sizes.","neon-channel-product-customizer-starter"),
                        ]
                    ],
                    "mountingOptions"=>[
                        "tabHeader"=>__("Mountings","neon-channel-product-customizer-starter"),
                        "newMounting"=>__("Add new mounting","neon-channel-product-customizer-starter"),
                        "mountingItem"=>[
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"), 
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this mounting ?","neon-channel-product-customizer-starter"),
                        ],
                        "listMouting"=>
                        [
                            "noItem"=>__("No mounting currently added","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "mountingSetting"=>
                        [
                            "title"=>__("Title","neon-channel-product-customizer-starter"),
                            "description"=>__("Description","neon-channel-product-customizer-starter"),
                            "explain"=>__("Provide further details about your sizes.","neon-channel-product-customizer-starter"),
                        ]
        
                    ],
                    "customAdditionalOptions"=>[
                        "tabHeader"=>__("Additional Options","neon-channel-product-customizer-starter"),
                        "addNew"=>__("Add new additional option","neon-channel-product-customizer-starter"),
                        "newItem"=>[
                            "header" => __("Choose an Input Type","neon-channel-product-customizer-starter"),
                            "description"=> __("This text will display above the input options.","neon-channel-product-customizer-starter"),
                            "type"=>[
                                "header"=> __("Set the Label and Description","neon-channel-product-customizer-starter"),
                                "description"=> __("This text will display above the input options.","neon-channel-product-customizer-starter"),
                                "yesOrno" => __("Yes Or No","neon-channel-product-customizer-starter"),
                                "imageInput" => __("Image-Inputs","neon-channel-product-customizer-starter"),
                                "dropdown" => __("Dropdown","neon-channel-product-customizer-starter"),
                                "note" => __("Note","neon-channel-product-customizer-starter"),
                                "includeType" => __("Option included","neon-channel-product-customizer-starter"),
                                "input"=>[
                                    "label"=>__("Label","neon-channel-product-customizer-starter"),
                                    "description"=>__("Description","neon-channel-product-customizer-starter"),
                                ],
                            ],
                            "yesOrno"=>[
                                "header"=> __("Yes/No Input","neon-channel-product-customizer-starter"),
                                "title"=> __("Displays as two buttons side by side.","neon-channel-product-customizer-starter"),
                                "selectedVal"=> __("Selected Value","neon-channel-product-customizer-starter"),
                                "unSelectedVal"=> __("Unselected Value","neon-channel-product-customizer-starter"),
                                "defaultSelect"=>[
                                    "header"=> __("DEFAULT SELECTED VALUE","neon-channel-product-customizer-starter"),
                                    "description"=> __("Choose which value is highlighted by default when the product customiser initally displays.","neon-channel-product-customizer-starter"),
                                ],
                                "selected"=> __("Selected","neon-channel-product-customizer-starter"),
                                "unSelected"=> __("UnSelected","neon-channel-product-customizer-starter"),
                            ],
                            "imageInput"=>[
                                "header"=>__("Images as Input","neon-channel-product-customizer-starter"),
                                "description"=> __("Displays as a row of images that is selectable by the customer.","neon-channel-product-customizer-starter"),
                                "label" => __("Label","neon-channel-product-customizer-starter"),
                                "value" => __("Value(Required)","neon-channel-product-customizer-starter"),
                            ],
                            "dropdown"=>[
                                "header"=>__("Dropdown Input","neon-channel-product-customizer-starter"),
                                "description"=> __("Displays as a select list.","neon-channel-product-customizer-starter"),
                                "label" => __("Label","neon-channel-product-customizer-starter"),
                                "value" => __("Value(Required)","neon-channel-product-customizer-starter"),
                            ],
                            "note"=>[
                                "title"=>__("Note Input","neon-channel-product-customizer-starter"),
                                "description"=> __("Displays as a textarea field for customers to type any additional requirements.","neon-channel-product-customizer-starter"),
                                "label"=> __("Character limit (optional)","neon-channel-product-customizer-starter"),
                                "noteDescription"=> __("The maximum number of text characters for notes.","neon-channel-product-customizer-starter"),
                            ],
                        ],
                        "listAdditionalOptions"=>[
                            "noItem"=>__("No Additional Options currently added","neon-channel-product-customizer-starter"),
                            "label"=>__("Label","neon-channel-product-customizer-starter"),
                            "type"=>__("Type","neon-channel-product-customizer-starter"),
                            "action"=>__("Actions","neon-channel-product-customizer-starter"),
                        ],
                        "modal"=>[
                            "delete"=>__("Do you want to remove this custom additional option ?","neon-channel-product-customizer-starter"),
                        ],
                    ],
                ],
                "settings"=>[
                    "title"=>"Settings",
                    "general"=>[
                        "tabHeader"=>__("General","neon-channel-product-customizer-starter"),
                        "customizer"=>[
                            "title"=>__("Customizer Options","neon-channel-product-customizer-starter"), 
                            "measurementUnit"=>__("Measurement Unit","neon-channel-product-customizer-starter"),
                            "showHideMeasurements"=>__("Show/hide Measurements","neon-channel-product-customizer-starter"),
                            "decimalFormatofMeasurements"=>__("Decimal Format of Measurements","neon-channel-product-customizer-starter"),
                            "desktop"=>[
                                "label"=>__("Desktop Column Order","neon-channel-product-customizer-starter"),
                                "description"=>__("Display the sidebar with all product options on the right or left hand side (this option only applies to desktop).","neon-channel-product-customizer-starter"),
                            ],
                            "button"=>[
                                "label"=>__("Show Switch button for Day and Night","neon-channel-product-customizer-starter"),
                                "description"=>__("Display a switch to turn default background into day or night mode.","neon-channel-product-customizer-starter"),
                            ]
                        ],
                        "mobile"=>[
                            "title"=>__("mobileOptions","neon-channel-product-customizer-starter"), 
                            "menuOnMobile"=>[
                                "title"=>__("Show Navigation Menu on Mobile","neon-channel-product-customizer-starter"),
                                "description"=>__("Display a navigation menu of the selections on mobile.","neon-channel-product-customizer-starter"),
                            ],
                            "menuFirst"=>[
                                "title"=>__("Show Navigation Menu First","neon-channel-product-customizer-starter"),
                                "description"=>__("This allows the users to jump to a specific selection from the navigation menu first. Otherwise, the screen will show the first selection.","neon-channel-product-customizer-starter"),
                            ],
                            "mobileSelection"=>[
                                "title"=>__("Mobile Selection Options Display","neon-channel-product-customizer-starter"),
                                "description"=>__("Allow selection options to display as horizontally scrollable options on mobile or stacked vertically.","neon-channel-product-customizer-starter"),
                            ] 
                        ],
                        "product"=>[
                            "title"=>__("Product","neon-channel-product-customizer-starter"),
                            "no"=>__("No","neon-channel-product-customizer-starter"),
                            "yes"=>__("Yes","neon-channel-product-customizer-starter"),
                            "redirectAfter"=>[
                                "title"=>__("Redirect to cart page after adding custom design to cart","neon-channel-product-customizer-starter"),
                                "description"=>__("This option allows you to choose to redirect the customer to the cart page after adding the customization to the shopping cart.","neon-channel-product-customizer-starter"),
                            ],
                            "redirectToCheckOut"=>[
                                "title"=>__("Redirect to checkout page after adding custom design to cart","neon-channel-product-customizer-starter"),
                                "description"=>__("This option allows you to choose to redirect the customer to the checkout page after adding the customization to the shopping cart.","neon-channel-product-customizer-starter"),
                            ],
                            "displayRecapsOnCheckout"=>[
                                "title"=>__("Display configuration data on the checkout page","neon-channel-product-customizer-starter"),
                                "description"=>__("This option allows you to choose whether or not to display configuration data to the customer on the checkout page.","neon-channel-product-customizer-starter"),
                            ],
                            "hideAddToCartOnSinglePage"=>[
                                "title"=>__("Hide add to cart button for customizable products on product details page","neon-channel-product-customizer-starter"),
                                "description"=>__("This option allows you to define whether or not you want to hide the add-to-cart button for the customizable product on the product page.","neon-channel-product-customizer-starter"),
                            ],
                            "hideAddToCartOnShopPage"=>[
                                "title"=>__("Hide the add to cart button for the customizable product on the shop page","neon-channel-product-customizer-starter"),
                                "description"=>__("This option lets you define whether or not you want to hide the add-to-cart button for the customizable product on the shop page.","neon-channel-product-customizer-starter"),
                            ],
                            "hideDesign"=>[
                                "title"=>__("Hide design buttons on shop page","neon-channel-product-customizer-starter"),
                                "description"=>__(" This option allows you to show/hide the customization button on the shop page.","neon-channel-product-customizer-starter"),
                            ]
                        ],
                        "output"=>[
                            "title"=>__("Output","neon-channel-product-customizer-starter"),
                            "format"=>[
                                "title"=>__("Output files format","neon-channel-product-customizer-starter"),
                                "description"=>__("What is your desired output files format ? ","neon-channel-product-customizer-starter"),
                            ],
                            "dimensions"=>[
                                "title"=>__("Output files dimensions","neon-channel-product-customizer-starter"),
                                "description"=>__("What are you desired output files dimensions ?","neon-channel-product-customizer-starter"),
                                "outputwidth"=>__("Output width","neon-channel-product-customizer-starter"),
                                "outputHeight"=>__("Output height","neon-channel-product-customizer-starter"),
                                "unit"=>__("Unit","neon-channel-product-customizer-starter"),
                                "useDimensions"=>__("Use dimensions","neon-channel-product-customizer-starter"),
                            ],
                            "sendDesign"=>[
                                "title"=>__("Send Design By Email","neon-channel-product-customizer-starter"),
                                "no" => __("No","neon-channel-product-customizer-starter"),
                                "yes"=>__("Yes","neon-channel-product-customizer-starter"),
                                "enable"=>[
                                    "title"=>__("Manufacturer Email","neon-channel-product-customizer-starter"),
                                    "description"=>__("An email is sent every time a custom sign is marked as PAID on the shopify order.","neon-channel-product-customizer-starter"),
                                    "instruction"=>__("Receiver email (if you have more than one, please separate them with '|' )","neon-channel-product-customizer-starter"),
                                    "explain"=>__("The store’s default email will be used if kept blank","neon-channel-product-customizer-starter"),
                                    "mailSubject"=>__("Mail Subject","neon-channel-product-customizer-starter"),
                                    "manufacturerEmailTemplate"=>__("Manufacturer email template","neon-channel-product-customizer-starter"),
                                ],
                            ],
                        ],
                        
                    ],
                    "languageImage"=>[
                        "tabHeader"=>__("Language and Image","neon-channel-product-customizer-starter"),
                        "main"=>[
                            "title"=>__("Main","neon-channel-product-customizer-starter"), 
                            "header"=>__("Header","neon-channel-product-customizer-starter"),
                            "textox"=>__("Text Box Label","neon-channel-product-customizer-starter"),
                            "line"=>[
                                "title"=>__("Line count","neon-channel-product-customizer-starter"),
                                "description"=>__("Preceded by the current number of lines over the total allowable (e.g. 1/3 lines).","neon-channel-product-customizer-starter"),
                            ],
                            "maxChar"=>[
                                "title"=>__("Max characters","neon-channel-product-customizer-starter"),
                                "description"=>__("Preceded by the maximum number of characters per line (e.g. 20 characters max per line for current size).","neon-channel-product-customizer-starter"),
                            ],
                            "priceButtonSection"=>[
                                "title"=>__("Price and button section","neon-channel-product-customizer-starter"),
                                "description"=>__("This section refers to the sticky add to cart / review button section located at the bottom of app","neon-channel-product-customizer-starter"),
                            ],
                            "addToCart"=>[
                                "title"=>__("Add to cart button","neon-channel-product-customizer-starter"),
                                "description"=>__("Add To cart Button Label.","neon-channel-product-customizer-starter"),
                            ],
                            "finishButton"=>[
                                "title"=>__("Finish button","neon-channel-product-customizer-starter"),
                                "description"=>__("Finish button Label.","neon-channel-product-customizer-starter"),
                            ],
                            "textBefore"=>[
                                "title"=>__("Text before price value","neon-channel-product-customizer-starter"),
                                "description"=>__("Displays before the price value","neon-channel-product-customizer-starter"),
                            ],
                            "textAfter"=>[
                                "title"=>__("Text after price value","neon-channel-product-customizer-starter"),
                                "description"=>__("Displays after the price value","neon-channel-product-customizer-starter"),
                            ],
                            "textAlignment"=>[
                                "title"=>__("Text Align","neon-channel-product-customizer-starter"),
                                "description"=>__("display instead of Text Align","neon-channel-product-customizer-starter"),
                            ],
                            "textAlignmentValue"=>[
                                "title"=>__("Text Align values","neon-channel-product-customizer-starter"),
                                "description"=>__("display instead of Text Align","neon-channel-product-customizer-starter"),
                                "left"=>__("Left","neon-channel-product-customizer-starter"),
                                "center"=>__("Center","neon-channel-product-customizer-starter"),
                                "right"=>__("Right","neon-channel-product-customizer-starter"),
                            ],
                            "measurementLabel"=>[
                                "title"=>__("Measurement label","neon-channel-product-customizer-starter"),
                                "description"=>__("Text to be displayed in the summary for height and width","neon-channel-product-customizer-starter"),
                                "height"=>__("Height","neon-channel-product-customizer-starter"),
                                "width"=>__("width","neon-channel-product-customizer-starter"),
                            ],
                            "textSummary"=>[
                                "title"=>__("Summary","neon-channel-product-customizer-starter"),
                                "description"=>__("Summary header text","neon-channel-product-customizer-starter"),
                            ],
                            "textEdit"=>[
                                "title"=>__("Edit","neon-channel-product-customizer-starter"),
                                "description"=>__("Edit button text","neon-channel-product-customizer-starter"),
                            ],
                            "modals"=>[
                                "title" => __("Size displacement modal texts","neon-channel-product-customizer-starter"),
                                "width"=>[
                                    "title"=>__("Maximum Width overrun","neon-channel-product-customizer-starter"),
                                   "description"=>__("Text to be displayed in the modal to show that the accepted width has been exceeded or reached","neon-channel-product-customizer-starter")
                                ],
                                "height"=>[
                                    "title"=>__("Maximum Height overrun","neon-channel-product-customizer-starter"),
                                   "description"=>__("Text to be displayed in the modal to show that the accepted height has been exceeded or reached","neon-channel-product-customizer-starter"),
                                ]
                            ],
                            "sectionText"=>[
                                "title"=>__("Text","neon-channel-product-customizer-starter"),
                                "description"=>__("Text to display on the default skin for the text section","neon-channel-product-customizer-starter"),
                            ],
                            "sectionAdditionalOptions"=>[
                                "title"=>__("Additionals Options","neon-channel-product-customizer-starter"),
                                "description"=>__("Text that will be displayed as the title for the additional options section","neon-channel-product-customizer-starter"),
                            ],
                            "charactersRemaining"=>[
                                "title"=>__("Remaining characters","neon-channel-product-customizer-starter"),
                                "description"=>__("Text to display for number of characters remaining","neon-channel-product-customizer-starter"),
                            ],
                            "minimumCharacter"=>[
                                "title"=>__("Minimum","neon-channel-product-customizer-starter"),
                                "description"=>__("Preceded by the minimum number of characters for the size (for example 20 characters minimum for the current size).","neon-channel-product-customizer-starter"),
                            ]
                        ],
                        "customDesign"=>[
                            "title"=>__("Customizer design","neon-channel-product-customizer-starter"), 
                            "customDesignLink"=>__("Custom design link","neon-channel-product-customizer-starter"),
                            "enableLink"=>[
                                "enable"=>__("Enable custom design link","neon-channel-product-customizer-starter"),
                                "description"=>__("Enable this to display a link to direct customers to another page on your site, this will display as one of the first options on desktop and mobile.","neon-channel-product-customizer-starter"),
                            ],
                           "link"=>[
                                "title"=>__("Custom Design Link","neon-channel-product-customizer-starter"),
                                "description"=>__("URL to redirect customers on your store that will allow for more complex graphic design quote submissions.","neon-channel-product-customizer-starter"),
                            ],
                            "phrase"=>__("Phrase for link to submit custom design page","neon-channel-product-customizer-starter"),
                        ],
                        "visualizer"=>[
                            "title"=>__("Visualizer","neon-channel-product-customizer-starter"), 
                            "default"=>[
                                "title"=>__("Default Text","neon-channel-product-customizer-starter"), 
                                "description"=>__("This will be displayed as the signs text when the visualizer is first loaded","neon-channel-product-customizer-starter"),
                            ],
            
                            "button"=>__("On/Off buttons","neon-channel-product-customizer-starter"), 
                            "on"=>__("Visualizer On","neon-channel-product-customizer-starter"), 
                            "off"=>__("Visualizer Off","neon-channel-product-customizer-starter"),
                            "light"=>__("Visualizer Light","neon-channel-product-customizer-starter"), 
                            "dark"=>__("Visualizer Dark","neon-channel-product-customizer-starter"),
                            "icons"=>[
                                "title"=>[
                                    "textIcon" => __("Text Icon","neon-channel-product-customizer-starter"),
                                    "fontIcon" => __("Font Icon","neon-channel-product-customizer-starter"),
                                    "sizeIcon" => __("size Icon","neon-channel-product-customizer-starter"),
                                    "letterTypeIcon" => __("Letter Type Icon","neon-channel-product-customizer-starter"),
                                    "colorIcon" => __("Color Icon","neon-channel-product-customizer-starter"),
                                    "materialIcon"=> __("Material Icon","neon-channel-product-customizer-starter"),
                                    "jacketIcon"=> __("Jacket Icon","neon-channel-product-customizer-starter"),
                                    "mountingIcon"=> __("Mounting Icon","neon-channel-product-customizer-starter"),
                                    "backboardIcon"=> __("Backboard Icon","neon-channel-product-customizer-starter"),
                                    "backboardColorIcon"=> __("Backboard Color Icon","neon-channel-product-customizer-starter"),
                                    "additionalIcon"=> __("Additional Options Icon","neon-channel-product-customizer-starter"),
                                ],
                                "description" => __("The icon to display on the skin on the page. Must be an svg code","neon-channel-product-customizer-starter"),
                            ]

                        ],
                        "images"=> [
                            "title"=>__("Images","neon-channel-product-customizer-starter"),
                            "headerPreview"=>__("Preview images","neon-channel-product-customizer-starter"), 
                            "headerReview"=>__("Review images","neon-channel-product-customizer-starter"), 
                            "preview"=>[
                                "title"=>__("Enable Preview Images","neon-channel-product-customizer-starter"), 
                                "description"=>__("If ticked, the preview images will be shown on the visualizer screen.","neon-channel-product-customizer-starter"),
                            ],
                            "display"=>[
                                "title"=>__("Display default blue/gray background image","neon-channel-product-customizer-starter"), 
                                "description"=>__("If ticked this image will display before other images","neon-channel-product-customizer-starter"),
                            ],
                            "reviewScreenImages"=>[
                                "title"=>__("Enable Review Screen Images","neon-channel-product-customizer-starter"), 
                                "description"=>__("If ticked, the preview images will be shown on the review screen.","neon-channel-product-customizer-starter"),
                            ],
                            "reviewImage"=>[
                                "title"=>__("Enable Review Image","neon-channel-product-customizer-starter"), 
                                "description"=>__("If option not ticked, the image showing the customization on the review screen will not be displayed.","neon-channel-product-customizer-starter"),
                            ],
                            "button"=>[
                                "title"=>__("choose the scenes","neon-channel-product-customizer-starter"),  
                            ]
                        ]
                    ],  
                    "themeColor"=>[
                        "tabHeader"=>__("Theme Color","neon-channel-product-customizer-starter"),
                        "tabSkin"=>__("Choose  Skin","neon-channel-product-customizer-starter"),
                        "tabCustomCSS"=>__("Custom CSS","neon-channel-product-customizer-starter"),
                        "chooseSkin"=>__("Choose your customizer appearance","neon-channel-product-customizer-starter"), 
                        "custom"=>__("Writing custom css","neon-channel-product-customizer-starter"), 
                        "customPlaceholder"=>__("Write custom css here","neon-channel-product-customizer-starter"), 
                        "settings"=> [
                            "title"=>__("Theme settings","neon-channel-product-customizer-starter"),
                            "openOption"=>[
                                "title"=>__("Leave options open","neon-channel-product-customizer-starter"),
                                "yes"=>__("yes","neon-channel-product-customizer-starter"),
                                "no"=>__("no","neon-channel-product-customizer-starter"),
                                "description"=>__("This option will allow you to leave all skin options open or not, even when they are not clicked.","neon-channel-product-customizer-starter")
                            ],
                        ],
                        "colors"=>[
                            "title"=>__("Theme colors","neon-channel-product-customizer-starter"),
                            "light"=>__("Light Mode","neon-channel-product-customizer-starter"),
                            "dark"=>__("Dark Mode","neon-channel-product-customizer-starter"),
                            "lightMode"=>[
                                "backgroundColor"=>__("Background Color","neon-channel-product-customizer-starter"),
                                "titleColor"=>__("Title Color","neon-channel-product-customizer-starter"),
                                "descriptionColor"=>__("Description Color","neon-channel-product-customizer-starter"),
                                "buttonHoverColor"=>__("Button Hover Color","neon-channel-product-customizer-starter"),
                                "buttonTextColor"=>__("Button Text Color","neon-channel-product-customizer-starter"),
                                "activeButtonColor"=>__("Active Button Color","neon-channel-product-customizer-starter"),
                                "activeButtonHoverColor"=>__("Active Button Hover Color","neon-channel-product-customizer-starter"),
                                "activeButtonTextColor"=>__("Active Button Text Color","neon-channel-product-customizer-starter"),
                                "beforePriceColor"=>__("Before Price Color","neon-channel-product-customizer-starter"),
                                "priceBackgroundColor"=>__("Price Background Color","neon-channel-product-customizer-starter"),
                                "priceColor"=>__("Price Color","neon-channel-product-customizer-starter"),
                                "afterPriceColor"=>__("After Price Color","neon-channel-product-customizer-starter"),
                                "optionsSectionBackgroundColor"=>__("Options Section Background Color","neon-channel-product-customizer-starter"),
                                "optionBorderColor"=>__("Option Border Color","neon-channel-product-customizer-starter"),
                                "optionTextColor"=>__("Option Text Color","neon-channel-product-customizer-starter"),
                                "activeOptionBorderColor"=>__("Active Option Border Color","neon-channel-product-customizer-starter"),
                                "activeOptionTextColor"=>__("Active Option Text Color","neon-channel-product-customizer-starter"),
                                "colorOfMeasuringBarText"=>__("Color of measuring bar Text","neon-channel-product-customizer-starter"),
                                "colorMeasuringBar"=>__("Color measuring bar","neon-channel-product-customizer-starter"),
                                "finishModalColor"=>__("Finish Modal Color ","neon-channel-product-customizer-starter"),
                                "finishModalLoaderColor"=>__("Finish Modal Loader Color ","neon-channel-product-customizer-starter"),
                            ],
                            "darkMode"=>[
                                "darkBackgroundColor"=>__("Dark Background Color","neon-channel-product-customizer-starter"),
                                "darkTitleColor"=>__("Title Color","neon-channel-product-customizer-starter"),
                                "darkDescriptionColor"=>__("Description Color","neon-channel-product-customizer-starter"),
                                "darkButtonHoverColor"=>__("Dark Button Hover Color","neon-channel-product-customizer-starter"),
                                "darkButtonTextColor"=>__("Dark Button Text Color","neon-channel-product-customizer-starter"),
                                "darkActiveButtonColor"=>__("Dark Active Button Color","neon-channel-product-customizer-starter"),
                                "darkActiveButtonHoverColor"=>__("Dark Active Button Hover Color","neon-channel-product-customizer-starter"),
                                "darkActiveButtonTextColor"=>__("Dark Active Button Text Color","neon-channel-product-customizer-starter"),
                                "darkBeforePriceColor"=>__("Dark Before Price Color","neon-channel-product-customizer-starter"),
                                "darkPriceBackgroundColor"=>__("Dark Price Background Color","neon-channel-product-customizer-starter"),
                                "darkPriceColor"=>__("Dark Price Color","neon-channel-product-customizer-starter"),
                                "darkAfterPriceColor"=>__("Dark After Price Color","neon-channel-product-customizer-starter"),
                                "darkOptionsSectionBackgroundColor"=>__("Dark Options Section Background Color","neon-channel-product-customizer-starter"),
                                "darkOptionBorderColor"=>__("Dark Option Border Color","neon-channel-product-customizer-starter"),
                                "darkOptionTextColor"=>__("Dark Option Text Color","neon-channel-product-customizer-starter"),
                                "darkActiveOptionBorderColor"=>__("Dark Active Option Border Color","neon-channel-product-customizer-starter"),
                                "darkActiveOptionTextColor"=>__("Dark Active Option Text Color","neon-channel-product-customizer-starter"),
                                "darkFinishModalColor"=>__("Finish Modal Color ","neon-channel-product-customizer-starter"),
                                "darkFinishModalLoaderColor"=>__("Finish Modal Loader Color ","neon-channel-product-customizer-starter"),
                            ]
                        ],
                    ],
                    "sortOption"=>[
                        "tabHeader"=>__("Sort Option Order","neon-channel-product-customizer-starter"),
                        "title"=>__("Order your configurator items","neon-channel-product-customizer-starter"), 
                        "orderItems"=>[
                            "textForm"=>__("Text Form","neon-channel-product-customizer-starter"),
                            "fonts"=>__("Fonts","neon-channel-product-customizer-starter"),  
                            "sizes"=>__("Sizes","neon-channel-product-customizer-starter"), 
                            "colors"=>__("Colors","neon-channel-product-customizer-starter"), 
                            "letterTypes"=>__("Letter Types","neon-channel-product-customizer-starter"), 
                            "materials"=>__("Materials","neon-channel-product-customizer-starter"), 
                            "jackets"=>__("Jackets","neon-channel-product-customizer-starter"), 
                            "mountings"=>__("Mounting","neon-channel-product-customizer-starter"), 
                            "backboards"=>__("Backboards","neon-channel-product-customizer-starter"), 
                            "backboardColors"=>__("BackboardColors","neon-channel-product-customizer-starter"), 
                            "additionalOptions"=>__("AdditionalOptions","neon-channel-product-customizer-starter"), 
                        ]
                    ]
                    
                ]        
            ],
            "globalSettings"=>[
                "title"=> __("Global Settings","neon-channel-product-customizer-starter"),
                "licenseTab"=>[
                    "label"=>__('License',"neon-channel-product-customizer-starter"),
                    "title"=>__('Enter the license key',"neon-channel-product-customizer-starter")
                ],
                "configuratorHeader"=>__("With which tag do you want the configurator title to be displayed?","neon-channel-product-customizer-starter"),
                "configPageTab"=>[
                    "label"=>__("Configuration Page","neon-channel-product-customizer-starter"),
                    "title"=>__("Select the configuration page","neon-channel-product-customizer-starter"),
                    "addNew"=>__("Add New", "neon-channel-product-customizer-starter"),
                    "modal"=>[
                        "title"=>__("Enter name of new configuration page","neon-channel-product-customizer-starter"),
                    ]
                ]
            ],
            "tutorial"=>[
                "title"=> __("Tutorial","neon-channel-product-customizer-starter"),
                "documentation"=>__("Documentation","neon-channel-product-customizer-starter"),
                "faq"=>__("FAQ","neon-channel-product-customizer-starter"),
                "support"=>__("Support","neon-channel-product-customizer-starter"),
            ],
            "demos"=>[
                "title"=> __("Demos","neon-channel-product-customizer-starter"),
                "theme"=>__("Theme","neon-channel-product-customizer-starter"),
                "productType"=>__("Product Type","neon-channel-product-customizer-starter"),
                "pricingMode"=>__("Pricing Mode","neon-channel-product-customizer-starter"),
            ],
            "webSite"=>[
                "title"=> __("Web Site","neon-channel-product-customizer-starter"),
            ]
        ];
    }

    /**
     * 
     */
    public function ncpc_add_custom_mime_types( $mimes ) {
		return array_merge(
			$mimes,
			array(
				'svg' => 'image/svg+xml',
				'ttf' => 'application/x-font-ttf',
				'woff2' => 'application/x-font-woff2',
				'woff' => 'application/x-font-woff',
				'otf' => 'application/x-font-otf',
				'icc' => 'application/vnd.iccprofile',
			)
		);
	}

    /**
	 * Check file type and extension.
	 *
	 * @param array  $data The data.
	 * @param mixed  $file The file.
	 * @param string $filename The file name.
	 * @param array $mimes The mimes.
	 * @param string $real_mime The real mimes.
	 * @return array
	 */
	public function ncpc_check_filetype_and_ext( $data, $file, $filename, $mimes, $real_mime ) {
		if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
			return $data;
		}

			$wp_file_type = wp_check_filetype( $filename, $mimes );

		// Check for the file type you want to enable, e.g. 'svg'.
		if ( 'ttf' === $wp_file_type['ext'] ) {
			$data['ext']  = 'ttf';
			$data['type'] = 'font/ttf';
		}

		if ( 'otf' === $wp_file_type['ext'] ) {
			$data['ext']  = 'otf';
			$data['type'] = 'font/otf';
		}

		if ( 'woff' === $wp_file_type['ext'] ) {
			$data['ext']  = 'woff';
			$data['type'] = 'font/woff';
		}

		if ( 'woff2' === $wp_file_type['ext'] ) {
			$data['ext']  = 'woff2';
			$data['type'] = 'font/woff2';
		}

		if ( 'svg' === $wp_file_type['svg'] ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

}

'use strict';
;(($) => {
    $(document).ready(() => {
        /**
         * wp_navigator plugin object
         */
        const wp_navigator = {};

        wp_navigator.substringMathcer = (string) => {
            return function findMatches(q, cb) {
                const matches = [];
                const subStringRegex = new RegExp(q, 'i');
                $.each(string, (i, str) => {
                    if (subStringRegex.test(str)) {
                        matches.push(str)
                    }
                })

                cb(matches);
            }
        }

        /**
         * wp_navigator modal container
         */
        wp_navigator.container = $(document);
    
        /**
         * Initialize the plugin events
         * 
         * @return void
         */
        wp_navigator.init = () => {
            console.log("wp_navigator plugin initialized")
            wp_navigator.menu = wp_navigator_plugin.full_menu;
            wp_navigator.submenu = wp_navigator_plugin.submenu;
            wp_navigator.triggerEvents();
        }
    
        /**
         * Trigger the plugin events
         * 
         * @return void
         */
        wp_navigator.triggerEvents = () => {
            $('#wp-navigator-search').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                name: 'menu',
                source: wp_navigator.substringMathcer(wp_navigator.menu),
                templates: {
                    suggestion: (data) => {
                        return `
                            <div class="tt-suggestions">
                                <a href="${data[2]}"><span class="dashicons ${data[6]}" style="margin-right:5px;"></span>${data[0]}</a>
                            </div>
                        `;
                    }
                }
            });

            // add event handler for showing the navigator modal        
            wp_navigator.keyDown((e) => {
                if ( e.ctrlKey && e.keyCode === 78 ) {
                    wp_navigator.showNavigatorModal();
                }
            })

            /**
             * Add event handler for hiding the modal
             */
            wp_navigator.container.on('click', function(event) {
                if (!$(event.target).closest('#wp-navigator-modal').length) {
                    // The click was outside of #wp-navigator-modal, so hide the modal
                    wp_navigator.hideNavigatorModal();
                }
            });
        }

        /**
         * Show the navigator modal
         * 
         * @return void
         */
        wp_navigator.showNavigatorModal = () => {
            const modal = $('#wp-navigator-modal');
            modal.show();
            modal.find('input').focus();
        }

        /**
         * Hide the navigator modal
         * 
         * @return void
         */
        wp_navigator.hideNavigatorModal = () => {
            const modal = $('#wp-navigator-modal');
            modal.hide();
        }

        /**
         * key up event handler
         * @param {*} callback 
         */
        wp_navigator.keyDown = (callback) => {
            wp_navigator.container.keyup((e) => {
                callback(e);
            })
        }

        wp_navigator.init();
    })
})(jQuery)
'use strict';
;(($) => {
    $(document).ready(() => {
        
        /**
         * wp_navigator plugin object
         */
        const wp_navigator = {};

        wp_navigator.keycodes = {
            ENTER: 13,
            UP: 38,
            DOWN: 40,
            ESCAPE: 27,
            N: 78,
        }

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
                        const icon = data[6];
                        const link = data[2];
                        const title = data[0];

                        // return empty if link is seperator1
                        if ( link === 'separator1' ) {
                            return '';
                        }

                        // give me the tabindex
                        let tabindex = 0;

                        // if the link is a submenu
                        if ( link === 'submenu' ) {
                            tabindex = -1;
                        }
                        
                        let html = `<div class="tt-suggestions" tabindex="${tabindex}">`;
                        html += `<a href="${data[2]}">`;
                        if ( data.includes(icon) ) {
                            html += `<span class="dashicons ${icon}" style="margin-right:5px;"></span>`;
                        }
                        html += `${title}</a></div>`;

                        return html;
                    }
                },
                display: (data) => {
                    return data[0];
                }
            })

            // Adjusted event handler for pressing enter on tt-cursor element
            $('.tt-suggestions').on('keydown', function(e) {
                // Check if the Enter key is pressed
                if (e.keyCode === wp_navigator.keycodes.ENTER || e.which === 13) {
                    console.log("Enter key pressed");
                    // Ensure the element is focused; this is a basic check, might need adjustment for complex scenarios
                    if ($(this).hasClass('tt-cursor')) {
                        console.log("Enter key pressed on focused element");
                        const link = $(this).find('a').attr('href');
                        if (link && link !== '#') {
                            window.location.href = link;
                            e.preventDefault(); // Prevent default to stop any further action
                        }
                    }
                }
            });

            // add event handler for showing the navigator modal
            wp_navigator.keyDown((e) => {
                if ( 
                    (e.ctrlKey && e.keyCode === wp_navigator.keycodes.N) || 
                    (e.keyCode === wp_navigator.keycodes.ESCAPE) 
                ) {
                    wp_navigator.toggleNavigatorModal();
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
        wp_navigator.toggleNavigatorModal = () => {
            const modal = $('#wp-navigator-modal');
            modal.toggle();
            if (modal.is(":visible")) {
                modal.find('input').focus(); // set focus when showing the modal
            }
            modal.find('input').val() // reset input value
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
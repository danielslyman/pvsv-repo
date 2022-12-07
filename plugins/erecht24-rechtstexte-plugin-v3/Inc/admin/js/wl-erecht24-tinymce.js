(function () {
    tinymce.create("tinymce.plugins.erecht24", {

        //url argument holds the absolute url of our plugin directory
        init: function (aEditor, url) {


            aEditor.addButton('erecht24', {
                type: 'menubutton',
                text: 'eRecht24',
                icon: 'erecht24',
                menu: [
                    {
                        text: 'Impressum (deutsch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="imprint" lang="de" strip_title="false"]');
                        }
                    },
                    {
                        text: 'Impressum (englisch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="imprint" lang="en" strip_title="false"]');
                        }
                    },
                    {
                        text: 'Datenschutzerklärung (deutsch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="privacy_policy" lang="de" strip_title="false"]');
                        }
                    },
                    {
                        text: 'Datenschutzerklärung (englisch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="privacy_policy" lang="en" strip_title="false"]');
                        }
                    },
                    {
                        text: 'Datenschutzerklärung für Social Media (deutsch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="privacy_policy_social_media" lang="de" strip_title="false"]');
                        }
                    },
                    {
                        text: 'Datenschutzerklärung für Social Media (englisch)',
                        onclick: function () {
                            aEditor.insertContent('[erecht24 type="privacy_policy_social_media" lang="en" strip_title="false"]');
                        }
                    }
                ]
            });

        },

        createControl: function (n, cm) {
            return null;
        },

        getInfo: function () {
            return {
                longname: "eRecht24 LegalTexts Plugin",
                author: "eRecht24",
                version: "2.0.0"
            };
        }
    });

    tinymce.PluginManager.add("erecht24", tinymce.plugins.erecht24);
})();

/* global SKApp, define, console, Backbone */
var SKEmail;

define([] ,function() {
    "use strict";
    /**
     * @class SKEmail
     * @augments Backbone.Model
     */
    SKEmail = Backbone.Model.extend({
        /**
         * @property mySqlId
         * @type integer
         * @default undefined
         */
        mySqlId: undefined,
        
        /**
         * @property code
         * @type string, 'M1', 'MS2'
         * @default undefined
         */
        code: undefined,

        /**
         * @property constructorCode
         * @type string, 'R1', 'B1'
         * @default undefined
         */
        constructorCode: undefined,

        /**
         * @property text
         * @type string, 'M1', 'MS2'
         * @default undefined
         */
        text: undefined,

        /**
         * @property previouseEmailText
         * @type string, 'M1', 'MS2'
         * @default undefined
         */
        previouseEmailText: undefined,

        /**
         * @property subject
         * @type SkMAilSubject
         * @default undefined
         */
        subject: undefined,

        /**
         * @property recipients
         * @type array of SKCharacter
         * @default undefined
         */
        recipients: [],

        /**
         * @property copyTo
         * @type array of SKCharacter
         * @default undefined
         */
        copyTo: [],
        
        /**
         * @property attachment
         * @type array of SKAttachment
         * @default undefined
         */
        attachment: undefined,
        
        /**
         * @property phrases
         * @type array of SKMailPhrases
         * @default undefined
         */
        phrases: [],
        
        /**
         * Used in markReaded(), markUnreaded(), isReaded()
         *
         * @property is_readed
         * @type a bool
         * @default undefined
         */
        is_readed: false,

        /**
         * @property is_has_attachment
         * @type a bool
         * @default undefined
         */
        is_has_attachment: false,        
        
        // @var string, 
        sendedAt: undefined,
        
        /**
         * @property sender
         * @type array of SKMailCharacter
         * @default undefined
         */
        sender: undefined,
        
        /**
         * @todo: replace with link to SKCharacter
         * @property senderNameString
         * @type string
         * @default an empty string
         */
        senderNameString: '',

        /**
         * @todo: replace with link to SKCharacter
         * @property senderEmailString
         * @type string
         * @default an empty string
         */
        senderEmailString: '',

        /**
         * @todo: replace with link to SKCharacter
         * @property recipientNameString
         * @type string
         * @default an empty string
         */
        recipientNameString: '',

        /**
         * @todo: replace with link to SKCharacter
         * @property recipientEmailString
         * @type string
         * @default an empty string
         */
        recipientEmailString: '',

        /**
         * @todo: replace with link to SKCharacter
         * @property copyToString
         * @type string
         * @default an empty string
         */
        copyToString: '',

        /**
         * mailClient.aliasFolderXxx;
         * @property folderAlias
         * @type string
         * @default undefined
         */
        folderAlias: undefined,

        /**
         * 'reply', 'forward', 'new', 'replyAll'
         * @property folderAlias
         * @type string
         * @default undefined
         */
        letterType: undefined,

        /**
         * @method isSubjectValid
         * @returns {boolean}
         */
        isSubjectValid: function() {
            try {
                // keep not strong compartion in non strong way!
                if (undefined === this.subject) { console.log('this.subject is undefined!') };
                if (undefined === this.subject.characterSubjectId) { console.log('this.subject.characterSubjectId is undefined!') };
                if (0 === this.subject.characterSubjectId) { console.log('this.subject.characterSubjectId = 0 !') };
                if ('0' === this.subject.characterSubjectId) { console.log('this.subject.characterSubjectId is "0"!') };
                if ('' === this.subject.text) { console.log('this.subject.text is empty text!') };
                if (undefined === this.subject.text) { console.log('this.subject.text is undefined!') };

                return (undefined !== this.subject &&
                    undefined !== this.subject.characterSubjectId &&
                    '0' !== this.subject.characterSubjectId &&
                    0 !== this.subject.characterSubjectId &&
                    '' !== this.subject.text &&
                    undefined !== this.subject.text );
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },


        /**
         * @method updateStatusPropertiesAccordingObjects: function() {
         * @return void
         */
        updateStatusPropertiesAccordingObjects: function() {
            try {
                if (undefined !== this.attachment) {
                    this.is_has_attachment = true;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setSenderEmailAndNameStrings
         * @param string
         * @return void
         */
        setSenderEmailAndNameStrings: function(string) {
            try {
                var senders = string.split(',');
                for(var i in senders){
                    var senderNameString = senders[i].substring(0, senders[i].indexOf('<', senders[i])).trim();
                    this.senderNameString += ((parseInt(i, 0) === 0)?'':' ,')+senderNameString;
                    this.senderEmailString += ((parseInt(i, 0) === 0)?'':' ,')+senders[i].replace('<', '').replace('>', '').replace(senderNameString, '').trim();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setRecipientEmailAndNameStrings
         * @param string
         * @return void
         */
        setRecipientEmailAndNameStrings: function(string) {
            try {
                var recipients = string.split(',');
                for(var i in recipients){
                    var recipientNameString = recipients[i].substring(0, recipients[i].indexOf('<', recipients[i])).trim();
                    this.recipientNameString += ((parseInt(i, 0) === 0)?'':' ,')+recipientNameString;
                    this.recipientEmailString += ((parseInt(i, 0) === 0)?'':' ,')+recipients[i].replace('<', '').replace('>', '').replace(recipientNameString, '').trim();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method addSenderEmailAndNameStrings
         * @param string
         * @return void
         */
        addSenderEmailAndNameStrings: function(string) {
            try {
                var separator = '';
                if ('' !== this.senderNameString) {
                   separator = ' ,';
                }
                this.senderNameString += separator + string.substring(0, string.indexOf('<', string)).trim();

                var separator2 = '';
                if ('' !== this.senderNameString) {
                   separator2 = ' ,';
                }
                this.senderEmailString += separator2 + string.replace('<', '').replace('>', '').replace(this.senderNameString, '').trim();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method addRecipientEmailAndNameStrings
         * @param string
         * @return void
         */
        addRecipientEmailAndNameStrings: function(string) {
            try {
                var separator = '';
                if ('' !== this.recipientNameString) {
                   separator = ' ,';
                }
                this.recipientNameString += separator + string.substring(0, string.indexOf('<', string)).trim();

                var separator2 = '';
                if ('' !== this.recipientNameString) {
                   separator2 = ' ,';
                }
                this.recipientEmailString += separator2 + string.replace('<', '').replace('>', '').replace(this.recipientNameString, '').trim();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method addCopyEmailAndNameStrings
         * @param string
         * @return void
         */
        addCopyEmailAndNameStrings: function(string) {
            try {
                var separator = '';
                if ('' !== this.copyToString) {
                    separator = ' ,';
                }
                this.copyToString += separator + string.substring(0, string.indexOf('<', string)).trim();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method isRead
         * @returns {boolean}
         */
        isRead: function() {
            try {
                return Boolean(this.is_readed);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        
        /**
         * @method getSubjectText
         * @return string
         */
        getSubjectText: function() {
            try {
                return this.subject.getText();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        
        /**
         * @method getIsReadCssClass
         * @return string, CSS style
         */
        getIsReadCssClass: function() {
            try {
                if (true === Boolean(this.is_readed)) {
                    return '';
                } else {
                    return ' notreaded ';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        
        /**
         * @method getIsHasAttachment
         * @return string, CSS style
         */
        getIsHasAttachment: function() {
            try {
                if (this.is_has_attachment) {
                    return '1';
                } else {
                    return '0';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        
        /**
         * @method getIsHasAttachmentCss
         * @return string, CSS style
         */
        getIsHasAttachmentCss: function() {
            try {
                if (this.is_has_attachment) {
                    return ' display: inline-block; ';
                } else {
                    return ' display: none; ';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

         /**
         * @method getAttachmentId
         * @returns {an empty string|integer}
         */
        getAttachmentId: function() {
            try {
                if ('undefined' === typeof this.attachment) {
                    return '';
                } else {
                    return this.attachment.fileMySqlId;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method getRecipientIdsString
         * @returns {string}
         */
        getRecipientIdsString: function() {
            var string = '';

            try {
                for (var i in this.recipients) {
                    string += this.recipients[i].get('id') + ',';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
            
            return string;
        },

        /**
         * @method getFormattedRecipientsString
         * @returns {string}
         */
        getFormattedRecipientsString: function() {
            var string = '';

            try {
                for (var i in this.recipients) {
                    string += this.recipients[i].getFormattedRecipientLabel();
                }

                if ('' === string) {
                    string = this.recipientNameString;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
            
            return string;
        },

        /**
         * @method getCopyToIdsString: function() {
         * @returns {string}
         */
        getCopyToIdsString: function() {
            var string = '';

            try {
                for (var i in this.copyTo) {
                    string += this.copyTo[i].get('id') + ',';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
            
            return string;
        },

        /**
         * @method getPhrasesIdsString
         * @returns {string}
         */
        getPhrasesIdsString: function() {
            var string = '';

            try {
                for (var i in this.phrases) {
                    string += this.phrases[i].mySqlId + ',';
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
            
            return string;
        },

        /**
         * @method isDraft
         * @returns {Boolean}
         */
        isDraft: function() {
            try {
                return SKApp.simulation.mailClient.aliasFolderDrafts === this.folderAlias;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method isForward
         * @returns {Boolean}
         */
        isForward: function() {
            try {
                return SKApp.simulation.mailClient.letterTypeForward === this.letterType;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method isReplyAll
         * @returns {Boolean}
         */
        isReplyAll: function() {
            try {
                return SKApp.simulation.mailClient.letterTypeReplyAll === this.letterType;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method isReply
         * @returns {Boolean}
         */
        isReply: function() {
            try {
                return SKApp.simulation.mailClient.letterTypeReply === this.letterType;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * is letter type 'new'
         * @method isNew
         * @returns {Boolean}
         */
        isNew: function() {
            try {
                return SKApp.simulation.mailClient.letterTypeNew === this.letterType;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKEmail;
});

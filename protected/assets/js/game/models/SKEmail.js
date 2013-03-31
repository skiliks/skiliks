/* 
 * 
 */
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
         * @method isSubjectValid
         * @returns {boolean}
         */
        isSubjectValid: function() {
            // keep not strong compartion in non strong way!
            return (undefined !== this.subject &&
                undefined !== this.subject.characterSubjectId && 
                '0' !== this.subject.characterSubjectId && 
                0 !== this.subject.characterSubjectId && 
                '' !== this.subject.text &&
                undefined !== this.subject.text );
        },


        /**
         * @method updateStatusPropertiesAccordingObjects: function() {
         * @return void
         */
        updateStatusPropertiesAccordingObjects: function() {
            if (undefined !== this.attachment) {
                this.is_has_attachment = true;
            }  
        },

        /**
         * @method setSenderEmailAndNameStrings
         * @param string
         * @return void
         */
        setSenderEmailAndNameStrings: function(string) {
            var senders = string.split(',');
            for(var i in senders){
                var senderNameString = senders[i].substring(0, senders[i].indexOf('<', senders[i])).trim();
                this.senderNameString += ((parseInt(i, 0) === 0)?'':' ,')+senderNameString;
                this.senderEmailString += ((parseInt(i, 0) === 0)?'':' ,')+senders[i].replace('<', '').replace('>', '').replace(senderNameString, '').trim();
            }
        },

        /**
         * @method setRecipientEmailAndNameStrings
         * @param string
         * @return void
         */
        setRecipientEmailAndNameStrings: function(string) {
            var recipients = string.split(',');
            for(var i in recipients){
                var recipientNameString = recipients[i].substring(0, recipients[i].indexOf('<', recipients[i])).trim();
                this.recipientNameString += ((parseInt(i, 0) === 0)?'':' ,')+recipientNameString;
                this.recipientEmailString += ((parseInt(i, 0) === 0)?'':' ,')+recipients[i].replace('<', '').replace('>', '').replace(recipientNameString, '').trim();
            }

        },

        /**
         * @method addSenderEmailAndNameStrings
         * @param string
         * @return void
         */
        addSenderEmailAndNameStrings: function(string) {
            var separator = '';
            if ('' != this.senderNameString) {
               separator = ' ,'; 
            }
            this.senderNameString += separator + string.substring(0, string.indexOf('<', string)).trim();
            
            var separator = '';
            if ('' != this.senderNameString) {
               separator = ' ,'; 
            }
            this.senderEmailString += separator + string.replace('<', '').replace('>', '').replace(this.senderNameString, '').trim();
        },

        /**
         * @method addRecipientEmailAndNameStrings
         * @param string
         * @return void
         */
        addRecipientEmailAndNameStrings: function(string) {
            var separator = '';
            if ('' != this.recipientNameString) {
               separator = ' ,'; 
            }
            this.recipientNameString += separator + string.substring(0, string.indexOf('<', string)).trim();
            
            var separator = '';
            if ('' != this.recipientNameString) {
               separator = ' ,'; 
            }
            this.recipientEmailString += separator + string.replace('<', '').replace('>', '').replace(this.recipientNameString, '').trim();
        },

        /**
         * @method addCopyEmailAndNameStrings
         * @param string
         * @return void
         */
        addCopyEmailAndNameStrings: function(string) {
            var separator = '';
            if ('' != this.copyToString) {
                separator = ' ,';
            }
            this.copyToString += separator + string.substring(0, string.indexOf('<', string)).trim();
        },

        /**
         * @method isRead
         * @returns {boolean}
         */
        isRead: function() {
            return this.is_readed == 1;
        },
        
        /**
         * @method getSubjectText
         * @return string
         */
        getSubjectText: function() {
            return this.subject.getText();
        },
        
        /**
         * @method getIsReadCssClass
         * @return string, CSS style
         */
        getIsReadCssClass: function() {
            if (true == this.is_readed) {
                return '';
            } else {
                return ' notreaded ';
            }
        },
        
        /**
         * @method getIsHasAttachment
         * @return string, CSS style
         */
        getIsHasAttachment: function() {
            if (this.is_has_attachment) {
                return '1';
            } else {
                return '0';
            }
        },
        
        /**
         * @method getIsHasAttachmentCss
         * @return string, CSS style
         */
        getIsHasAttachmentCss: function() {
            if (this.is_has_attachment) {
                return ' display: inline-block; ';
            } else {
                return ' display: none; ';
            }
        },

        /**
         * @method isValid
         * @returns {boolean}
         */
        isValid: function() {
            
            if (undefined === this.subject) {
                throw 'Письмо должно содержать тему.';
            }
                
            return true;
        },

        /**
         * @method getAttachmentId
         * @returns {an empty string|integer}
         */
        getAttachmentId: function() {
            if ('undefined' === typeof this.attachment) {
                return '';
            } else {
                return this.attachment.fileMySqlId;
            }
        },

        /**
         * @method getRecipientIdsString
         * @returns {string}
         */
        getRecipientIdsString: function() {
            var string = '';
            for (var i in this.recipients) {
                string += this.recipients[i].mySqlId + ',';
            }
            
            return string;
        },

        /**
         * @method getFormattedRecipientsString
         * @returns {string}
         */
        getFormattedRecipientsString: function() {
            var string = '';
            for (var i in this.recipients) {
                string += this.recipients[i].getFormattedRecipientLabel();
            }
            
            if ('' == string) {
                string = this.recipientNameString;
            }
            
            return string;
        },

        /**
         * @method getCopyToIdsString: function() {
         * @returns {string}
         */
        getCopyToIdsString: function() {
            var string = '';
            for (var i in this.copyTo) {
                string += this.copyTo[i].mySqlId + ',';
            }
            
            return string;
        },

        /**
         * @method getPhrasesIdsString
         * @returns {string}
         */
        getPhrasesIdsString: function() {
            var string = '';
            for (var i in this.phrases) {
                string += this.phrases[i].mySqlId + ',';
            }
            
            return string;
        }
    });

    return SKEmail;
});

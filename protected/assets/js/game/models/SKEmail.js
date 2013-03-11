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
        // @var integer, MySQL id

        mySqlId: undefined,
        
        // @var string, 'M1', 'MS2'
        code: undefined,

        // @var string, 
        constructorCode: undefined,

        // @var string, 
        text: undefined,

        // @var string, 
        previouseEmailText: undefined,

        // @var instance of SkMAilSubject, 
        subject: undefined,

        // @var array of SkCharacter
        recipients: [],
        
        // @var array of SkCharacter
        copyTo: [],
        
        // @var array of SkAttachment
        attachment: undefined,
        
        // @var array of SkMailPhrases
        phrases: [],
        
        // @var bool, use markReaded(), markUnreaded(), isReaded()
        is_readed: false,
        
        // @var bool
        is_has_attachment: false,        
        
        // @var string, 
        sendedAt: undefined,
        
        // @var SKMailCharacter
        // we need sender id fron server:
        // use instead of senderNameString, after refactiring will be complete
        sender: undefined,
        
        // @var string,
        // @todo: replace with link to SKCharacter
        senderNameString: '',
        
        // @var string,
        // @todo: replace with link to SKCharacter
        senderEmailString: '',
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientNameString: '',
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientEmailString: '',
        
        // @var string, 
        // @todo: replace with links to SKCharacters
        copyToString: '',

        /**
         * @method
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
         * @method
         * @param fullMinutes
         */
        setSendedAtFromTodayMinutes: function(fullMinutes) {
            var hours = (Math.floor(fullMinutes/60));
            var minutes = (fullMinutes - hours*60);
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            this.sendedAt = '10.03.2012 ' + hours + ':' + minutes;
        },

        /**
         * @method
         */
        updateStatusPropertiesAccordingObjects: function() {
            if (undefined !== this.attachment) {
                this.is_has_attachment = true;
            }  
        },

        /**
         * @method
         * @param string
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
         * @method
         * @param string
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
         * @method
         * @param string
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
         * @method
         * @param string
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
         * @method
         * @param string
         */
        addCopyEmailAndNameStrings: function(string) {
            var separator = '';
            if ('' != this.copyToString) {
                separator = ' ,';
            }
            this.copyToString += separator + string.substring(0, string.indexOf('<', string)).trim();
        },

        /**
         * @method
         */
        markReaded: function() {
            this.is_readed = 1;
        },

        /**
         * @method
         */
        markUnreaded: function() {
            this.is_readed = 0;
        },

        /**
         * @method
         * @returns {boolean}
         */
        isReaded: function() {
            return this.is_readed == 1;
        },
        
        /**
         * @method
         * @return string
         */
        getSubjectText: function() {
            return this.subject.getText();
        },
        
        /**
         * @method
         * @return string, CSS style
         */
        getIsReadedCssClass: function() {
            if (true == this.is_readed) {
                return '';
            } else {
                return ' notreaded ';
            }
        },
        
        /**
         * @method
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
         * @method
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
         * @method
         * @returns {boolean}
         */
        isValid: function() {
            
            if (undefined === this.subject) {
                throw 'Письмо должно содержать тему.';
            }
                
            return true;
        },

        /**
         * @method
         * @returns {*}
         */
        getAttachmentId: function() {
            if ('undefined' === typeof this.attachment) {
                return '';
            } else {
                return this.attachment.fileMySqlId;
            }
        },

        /**
         * @method
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
         * @method
         * @returns {string}
         */
        getFormatedRecipientsString: function() {
            var string = '';
            for (var i in this.recipients) {
                string += this.recipients[i].getFormated_2_ForMailToName();
            }
            
            if ('' == string) {
                string = this.recipientNameString;
            }
            
            return string;
        },

        /**
         * @method
         * @returns {string}
         */
        getFormatedCopyToString: function() {
            var string = '';
            for (var i in this.copyTo) {
                string += this.copyTo[i].getFormated_2_ForMailToName();
            }
            
            if ('' == string) {
                string = this.copyToNameString;
            }
            
            return string;
        },

        /**
         * @method
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
         * @method
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

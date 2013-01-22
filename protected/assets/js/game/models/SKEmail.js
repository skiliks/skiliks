/* 
 * 
 */
(function() {
    "use strict";
    window.SKEmail = Backbone.Model.extend({
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
        senderNameString: undefined,
        
        // @var string,
        // @todo: replace with link to SKCharacter
        senderEmailString: undefined,
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientNameString: undefined,
        
        // @var string, 
        // @todo: replace with link to SKCharacter
        recipientEmailString: undefined,
        
        // @var string, 
        // @todo: replace with links to SKCharacters
        copyToString: undefined, 
        
        isSubjectValid: function() {
            // keep not strong compartion in non strong way!
            return (undefined !== this.subject && 
                undefined !== this.subject.characterSubjectId && 
                '0' !== this.subject.characterSubjectId && 
                0 !== this.subject.characterSubjectId && 
                '' !== this.subject.text &&
                undefined !== this.subject.text );
        },
        
        setSendedAtFromTodayMinutes: function(fullMinutes) {
            var hours = (Math.floor(fullMinutes/60));
            var minutes = (fullMinutes - hours*60);
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            this.sendedAt = '10.03.2012 ' + hours + ':' + minutes;
        },
        
        updateStatusPropertiesAccordingObjects: function() {
            if (undefined !== this.attachment) {
                this.is_has_attachment = true;
            }  
        },
        
        setSenderEmailAndNameStrings: function(string) {
            this.senderNameString = string.substring(0, string.indexOf('<', string)).trim();
            this.senderEmailString = string.replace('<', '').replace('>', '').replace(this.senderNameString, '').trim();
        },
        
        setRecipientEmailAndNameStrings: function(string) {
            this.recipientNameString = string.substring(0, string.indexOf('<', string)).trim();
            this.recipientEmailString = string.replace('<', '').replace('>', '').replace(this.recipientNameString, '').trim();
        },

        /**
         */
        markReaded: function() {
            this.is_readed = 1;
        },

        /**
         */

        markUnreaded: function() {
            this.is_readed = 0;
        },

        /**
         */

        isReaded: function() {
            return this.is_readed == 1;
        },
        
        /**
         * return string
         */
        getSubjectText: function() {
            return this.subject.getText();
        },
        
        /**
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
         * @return string, CSS style
         */
        getIsHasAttachmentCss: function() {
            if (this.is_has_attachment) {
                return ' display: inline-block; ';
            } else {
                return ' display: none; ';
            }
        },
        
        isValid: function() {
            
            if (undefined === this.subject) {
                throw 'Письмо должно содержать тему.';
            }
                
            return true;
        },
        
        getAttachmentId: function() {
            if (undefined === typeof this.attachment) {
                return '';
            } else {
                return this.attachment.fileMySqlId;
            }
        },
        
        getRecipientIdsString: function() {
            var string = '';
            for (var i in this.recipients) {
                string += this.recipients[i].mySqlId + ',';
            }
            
            return string;
        },
        
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
        
        getCopyToIdsString: function() {
            var string = '';
            for (var i in this.copyTo) {
                string += this.copyTo[i].mySqlId + ',';
            }
            
            return string;
        },
        
        getPhrasesIdsString: function() {
            var string = '';
            for (var i in this.phrases) {
                string += this.phrases[i].mySqlId + ',';
            }
            
            return string;
        }
    });
})();

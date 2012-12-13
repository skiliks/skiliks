<?php

/**
 *
 * @author slavka
 */
class EmailData 
{
    public $email = null;
    
    public $isBigTask = false;  // more than 16 game minutes
    
    public $isSmallTask = false;  // less than 16 game minutes
    
    public $firstOpenedAt = null;
    
    public $answeredAt = null;
    
    public $answerEmailId = null;
    
    public $plannedAt = null;
    
    private $isSpam = false;
    
    private $planedTaskId = null;
    
    private $rightPlanedTaskId = null;
    
    public $typeOfImportance = null;
    
    /**
     * @param MailBox instance $email
     */
    public function __construct($email) {
        $this->email = $email;
        
        $this->isSmallTask = (bool)rand(0,1);
        $this->isBigTask = (bool)rand(0,1);
        $this->isSpam = (bool)rand(0,1);
    }
    
    // ----------
    
    /**
     * Checks is email was planed in $delta minutes after reading
     * 
     * @param integer $delta, game minutes
     * @return boolean
     */
    public function isPlanedByMinutes($delta) 
    {
        $isPlannedInTime = (strtotime($this->getPlanedAt()) - strtotime($this->getFirstOpenedAt())) <= $delta;
        
        return ($this->getIsPlaned() && $isPlannedInTime);
    }
    
    /**
     * Checks is email was replied in $delta minutes after reading
     * 
     * @param integer $delta, game minutes
     * @return boolean
     */
    public function isAnsweredByMinutes($delta) 
    {
        $isRepliedInTime = (strtotime($this->getAnsweredAt()) - strtotime($this->getFirstOpenedAt())) <= $delta;
        
        return ($this->getIsReplied() && $isRepliedInTime);
    }
    
    // ----------
    
    public function isNeedToBePlaned()
    {
        return 'plan' === $this->typeOfImportance;
    }
    
    public function isNeedToActInTwoMinutes()
    {
        return '2_min' === $this->typeOfImportance;
    }
    
    /**
     * @param $date string, 'plan', 'spam' .. etc.
     * 
     * @return EmaiData
     */    
    public function setTypeOfImportance($v) {
        $this->typeOfImportance = $v;
        
        return $this;
    }


    /**
     * @return boolean
     */
    public function getIsPlaned()
    {
        return (boolean)$this->email->plan;
    }
    
    /**
     * @return boolean
     */
    public function getIsReaded()
    {
        return (boolean)$this->email->readed;
    }
    
    /**
     * @return boolean
     */
    public function getIsReplied()
    {
        return (boolean)$this->email->reply;
    }
    
    /**
     * @return integer | null
     */    
    public function getParentEmailId()
    {
        return $this->email->message_id;
    }
    
    /**
     * @return boolean
     */
    public function getIsSpam()
    {
        return 'spam' === $this->typeOfImportance;
    }
    
    /**
     * @return integer | null
     */
    public function getEmailFolderId()
    {
        return $this->email->group;
    }
    
    /**
     * @return string, format 'hh:ii:ss'
     */
    public function getFirstOpenedAt() {
        return $this->firstOpenedAt;
    }
    
    /**
     * @param $date string, format 'hh:ii:ss'
     * 
     * @return EmaiData
     */    
    public function setFirstOpenedAt($date) {
        $this->firstOpenedAt = $date;
        
        return $this;
    }
    
    /**
     * @return string, format 'hh:ii:ss'
     */
    public function getPlanedAt() {
        return $this->plannedAt;
    }
    
    /**
     * @param $date string, format 'hh:ii:ss'
     * 
     * @return EmaiData
     */ 
    public function setPlanedAt($date) {
        $this->plannedAt = $date;
        
        return $this;
    }
    
    /**
     * @return string, format 'hh:ii:ss'
     */
    public function getAnsweredAt() {
        return $this->answeredAt;
    }
    
    /**
     * @param $date string, format 'hh:ii:ss'
     * 
     * @return EmaiData
     */ 
    public function setAnsweredAt($date) {
        $this->answeredAt = $date;
        
        return $this;
    }
    
    /**
     * @return integer mail_task.id
     */
    public function getPlanedTaskId() {
        return $this->planedTaskId;
    }
    
    /**
     * @param integer $id, mail_task.id
     * 
     * @return EmaiData
     */    
    public function setPlanedTaskId($id) {
        $this->planedTaskId = $id;
        
        return $this;
    }
    
    /**
     * @return integer mail_task.id
     */
    public function getRightPlanedTaskId() {
        return $this->rightPlanedTaskId;
    }
    
    /**
     * @param integer $id, mail_task.id
     * 
     * @return EmaiData
     */    
    public function setRightPlanedTaskId($id) {
        $this->rightPlanedTaskId = $id;
        
        return $this;
    }
}


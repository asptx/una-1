<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Forum Forum
 * @ingroup     UnaModules
 *
 * @{
 */

class BxForumCmts extends BxTemplCmts
{
    protected $MODULE;
    protected $_oModule;

    public function __construct($sSystem, $iId, $iInit = 1)
    {
    	$this->MODULE = 'bx_forum';
    	$this->_oModule = BxDolModule::getInstance($this->MODULE);

        $this->_sTableImages = 'bx_forum_files';

        parent::__construct($sSystem, $iId, $iInit, $this->_oModule->_oTemplate);

        $this->_bPostQuote = true;
    }

    public function registerTranscoders()
    {
        parent::registerTranscoders();

        $aTranscoders = array(
            $this->getTranscoderPreviewName()
        );

        BxDolTranscoderImage::registerHandlersArray($aTranscoders);
    }

    public function unregisterTranscoders()
    {
        parent::unregisterTranscoders();

        $aTranscoders = array(
            $this->getTranscoderPreviewName()
        );

        BxDolTranscoderImage::unregisterHandlersArray($aTranscoders);
        BxDolTranscoderImage::cleanupObjectsArray($aTranscoders);
    }

	public function isPostAllowed($isPerformAction = false)
    {
    	$aContentInfo = $this->_oModule->_oDb->getContentInfoById($this->_iId);
        if(!$aContentInfo || (int)$aContentInfo[$this->_oModule->_oConfig->CNF['FIELD_LOCK']] == 1)
            return false;

    	return parent::isPostAllowed($isPerformAction);
    }

    public function onPostAfter($iId)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $mixedResult = parent::onPostAfter($iId);
        if($mixedResult === false)
            return $mixedResult;

        if(getParam($CNF['PARAM_AUTOSUBSCRIBE_REPLIED']) == 'on')
            BxDolConnection::getObjectInstance($CNF['OBJECT_CONNECTION_SUBSCRIBERS'])->actionAdd((int)$this->getId(), (int)$this->_getAuthorId());

        return $mixedResult;
    }

    public function getCommentsBlock($aBp = array(), $aDp = array())
    {
        $mixedBlock = parent::getCommentsBlock($aBp, $aDp);
        if (is_array($mixedBlock) && isset($mixedBlock['title']))
            $mixedBlock['title'] = _t('_bx_forum_page_block_title_entry_comments', $this->getCommentsCount());
        return $mixedBlock;
    }

    public function getComment($mixedCmt, $aBp = array(), $aDp = array())
    {
        return parent::getComment($mixedCmt, $aBp, array_merge($aDp, array(
            'class_comment' => 'bx-def-box bx-def-padding bx-def-round-corners bx-def-color-bg-box'
        )));
    }

    protected function _getEmpty($aDp = array())
    {
        return parent::_getEmpty(array_merge($aDp, array(
            'class' => 'bx-def-box bx-def-padding bx-def-round-corners bx-def-color-bg-box'
        )));
    }

    protected function _getFormBox($sType, $aBp, $aDp)
    {
        if(!isset($aBp['parent_id']) || (int)$aBp['parent_id'] == 0)
            $aDp = array_merge($aDp, array(
                'class' => 'bx-def-box bx-def-padding bx-def-round-corners bx-def-color-bg-box'
            ));

        return parent::_getFormBox($sType, $aBp, $aDp);
    }

    protected function _getFormObject($sAction = BX_CMT_ACTION_POST)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $oResult = parent::_getFormObject($sAction);
        if(!isset($oResult->aInputs['cmt_image']))
            return $oResult;

        $oResult->aInputs['cmt_image']['storage_object'] = $CNF['OBJECT_STORAGE_CMTS']; 
        $oResult->aInputs['cmt_image']['images_transcoder'] = $CNF['OBJECT_IMAGES_TRANSCODER_PREVIEW_CMTS'];
        $oResult->aInputs['cmt_image']['upload_buttons_titles'] = array('Simple' => 'paperclip');

        return $oResult;
    }
}

/** @} */

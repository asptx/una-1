<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * Services for Wiki functionality
 */
class BxBaseServiceWiki extends BxDol
{

    /**
     * @page service Service Calls
     * @section bx_system_general System Services 
     * @subsection bx_system_general-wiki Wiki
     * @subsubsection bx_system_general-wiki_page wiki_page
     * 
     * @code bx_srv('system', 'wiki_page', ["index"], 'TemplServiceWiki'); @endcode
     * @code {{~system:wiki_page:TemplServiceWiki["index"]~}} @endcode
     * 
     * Display WIKI page.
     * @param $sUri categories object name
     * 
     * @see BxBaseServiceWiki::serviceWikiPage
     */
    /** 
     * @ref bx_system_general-wiki_page "wiki_page"
     */
    public function serviceWikiPage ($sWikiObject, $sUri)
    {
        $oWiki = BxDolWiki::getObjectInstance($sWikiObject);
        if (!$oWiki) {
            $oTemplate = BxDolTemplate::getInstance();
            $oTemplate->displayPageNotFound();
            return;
        }

        $oPage = BxDolPage::getObjectInstanceByModuleAndURI($sWikiObject, $sUri);
        if ($oPage) {

            $oPage->displayPage();

        } else {

            if ($oWiki->isAllowed('add')) {
                $oPage = BxDolPage::getObjectInstanceByURI($sUri);
                if ($oPage) {
                    $oTemplate = BxDolTemplate::getInstance();
                    $oTemplate->displayErrorOccured(_t("_sys_wiki_error_page_exists", bx_process_output($sUri)));
                } else {
                    echo "TODO: wiki - suggest to create page with specified URI";
                }
            } 
            else {
                $oTemplate = BxDolTemplate::getInstance();
                $oTemplate->displayPageNotFound();
            }
        }

    }
}

/** @} */
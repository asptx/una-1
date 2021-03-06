/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */
function BxDolPage(oOptions)
{
    this._sObjName = oOptions.sObjName == undefined ? 'oBxDolPage' : oOptions.sObjName;
    this._isStickyColumns = oOptions.isStickyColumns == undefined ? false : oOptions.isStickyColumns;
    this._iLastSc = 0;
    var $this = this;
    $(document).ready(function () {
        $this.init();
    });
}

BxDolPage.prototype.init = function () {
    var $this = this;
    if ($this._isStickyColumns && !$('html').hasClass('bx-media-phone')) {
        $(window).resize(function () { $this.stickyBlocks() });
        $(window).scroll(function () { $this.stickyBlocks() });
    }
};

BxDolPage.prototype.stickyBlocks = function () {
    iSc = $(window).scrollTop();
    $.each($('.bx-layout-col'), function (index, val) {
        if ($(this).css('position') == 'sticky') {
            var iCh = $(this).height();
            var iWh = $(window).height();

            if ($('#bx-toolbar').css('position') == 'fixed') {
                $(this).css('top', $('#bx-content-main').offset().top + 'px');
            }
            else {
                $(this).css('top', '60px');
            }

            if (iCh > iWh) {
                if (iCh - $(window).scrollTop() - $(window).height() < 0) {
                    var iMinS = (iWh - iCh);
                    if ($(this).css('top') == '') {
                        $(this).css('top', iMinS + 'px');
                    }
                    else {
                        var iCurS = new Number($(this).css('top').replace('px', '')) - iSc + this._iLastSc;
                        if (iCurS > 0) {
                            $(this).css('top', '0px');
                        }
                        if (iCurS < iMinS) {
                            $(this).css('top', iMinS + 'px');
                        }
                        if (iCurS < 0 && iCurS > iMinS) {
                            $(this).css('top', iCurS + 'px');
                        }
                    }
                }
            }
        }
    });
    this._iLastSc = iSc <= 0 ? 0 : iSc;
}


/** @} */

import {Field} from "../Field";
import CSS from "./css/select.module.scss";

export class Select extends Field {
    /**
     * @param {IAppContext} ctx
     * @param {Field|Object|null} obj
     * @returns {Select}
     */
    constructor(ctx,obj) {
        super(ctx,obj);
        this.options = (obj.hasOwnProperty('options')) ? obj.options : [];
        this.others = (obj.hasOwnProperty('others')) ? obj.others : null;

        this._optionRowEditHtml = function (value) {
            return `<div class="${CSS.optionRow}">
                <input type="text" class="form-control w-75 d-inline" value="${value}" placeholder="Add options"><span class="${CSS.delete}"><i class="far fa-trash-alt"></i></span>
                </div>`;
        }
        this._optionOthersRowEditHtml = function (value) {
            return `<div class="${CSS.optionRow} ${CSS.optionRowOthers}"><label>Others</label><input type="text" class="form-control w-75 d-inline" value="${value}" placeholder="Add Others"><span class="${CSS.delete}"><i class="far fa-trash-alt"></i></span></div>`;
        }

    }
    display() {
        let optionList = '<option value=""></option>',selectOthers = '',selectOthersPlaceholder='';
        let i = 0;
        for (; i < this.options.length; i++) {
            optionList += '<option value="'+this.options[i]+'">'+this.options[i]+'</option>';
        }
        if (this.others && i < this.others.length > 0) {
            optionList += '<option value="others">Others</option>';
            selectOthers += 'data-others-placeholder=""';
            selectOthersPlaceholder = this.others;

        }
        this.content.html = `<select class="form-control w-100" ${selectOthers}>${optionList}</select><div class="${CSS.fieldOptionOthers} d-none">Others<br><input class="w-75 form-control d-inline" placeholder="${selectOthersPlaceholder}"></div>`;
        return super.display();
    }

    edit() {
        let labelValue = '',optionList = '',showAddOthers = true;

        for (let i = 0; i < this.options.length; i++) {
            optionList += this._optionRowEditHtml(this.options[i]);
        }
        if (this.others && this.others.length > 0) {
            optionList += this._optionOthersRowEditHtml(this.others);
            showAddOthers = false
        }
        optionList = `<div class="${CSS.optionList}">${optionList}</div>`;
        this.content.html = `${optionList}<span class="primary-tint mr-3 ${CSS.addOption}">Add option</span><span class="primary-tint ${CSS.addOthersOption} cursor-pointer ${((showAddOthers) ? '':'d-none')}">Add others</span>`;

        return super.edit();
    }

    static get EnumType() {
        return 'select';
    }
    initDOMEvents(formContainer)
    {
        let self = this;
        formContainer.on('click', `.${CSS.addOption}`, function () {
            var optionList = $(this).prev(`.${CSS.optionList}`);
            var toInsert = self._optionRowEditHtml('');
            var optionOthers = optionList.children(`.${CSS.optionRowOthers}`);
            if (optionOthers.length > 0) {
                $(toInsert).insertBefore(optionOthers);
            } else {
                optionList.append(toInsert);

            }
        });

        formContainer.on('click', `.${CSS.addOthersOption}`, function () {
            $(this).siblings(`.${CSS.optionList}`).append(self._optionOthersRowEditHtml(''));
            $(this).addClass("d-none");
        });

        formContainer.on('click', `.${CSS.delete}`, function () {
            var parent = $(this).parents(`.${CSS.optionRow}`).first();
            if (parent.hasClass(CSS.optionRowOthers)) {
                parent.parent().siblings(`.${CSS.addOthersOption}`).removeClass("d-none");
            }
            parent.remove();
        });
        formContainer.on('change','select[data-others-placeholder]', function () {
            var fieldOther = $(this).siblings('.field-others');
            if ($(this).val() == 'others' ) {
                fieldOther.removeClass("d-none");

            } else {
                fieldOther.addClass("d-none");
            }
        });
    }

    /**
     * @param {jQuery} dom
     */
    updateFromDom(dom) {
        this.options = [];
        let self = this;
        dom.find(`.${CSS.optionRow}`).each(function (){
            if ($(this).hasClass(`.${CSS.optionRowOthers}`))
            {
                self.others = $(this).children('input').val();
            } else {
                self.options.push($(this).children('input').val());
                self.others = null;

            }
        });
    }

    saveToJSON() {
        let ret = super.saveToJSON();
        ret.options = this.options;
        ret.others = this.others;
        return ret;
    }

}



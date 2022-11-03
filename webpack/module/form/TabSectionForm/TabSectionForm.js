import 'module/form/builder/FormBuilder';
import {TabBuilder} from "module/form/builder/TabBuilder";
import {IBuilder} from "module/form/builder/IBuilder";
import {IAppContext} from "module/form/builder/IAppContext";
import CSS from './css/tabSectionForm.module.scss';
let $ = require('jquery');


export class TabSectionForm extends IAppContext {

    /**
     *
     * @param {jQuery} dom
     */
    constructor(dom) {
        super();
        dom.append(`<div class="${CSS.saveBtnWrapper}" style="display: none"><div class="${CSS.saveBtn} btn">Save</div></div><div class="float-right text-right">${this.getEditBtnHTML()}</div>`);
        this.tabBuilder = new TabBuilder(this, dom);
        this.tabBuilder.initDOMEvents();
        this.domContainer = dom;
        this.initDOMEvents();
    }

    /**
     * @param {string|Object} obj
     */
    loadFromJson(obj) {
        let json;
        if (typeof obj == "string") {
            json = JSON.parse(obj);
        }
        json = obj;
    }

    saveToJSON() {
        return this.tabBuilder.saveToJSON();
    }

    /**
     * @param {string} label
     * @return string
     */
    addTab(label) {
        return this.tabBuilder.addTabSection(label);
    }


    /**
     * @param uuid
     * @param label
     */
    addSection(tabUuid,label) {
        let tab = this.tabBuilder.loadTabWithUuid(tabUuid);
        tab.getContent().addSection(label);
    }

    showSaveBtn()
    {
        let btn = this.domContainer.find(`.${CSS.saveBtnWrapper}`);
        btn.show();

        this.domContainer.append(`<div class="${CSS.scrollFix}" style="height: 4em"></div>`);

    }

    hideSaveBtn()
    {
        let btn = this.domContainer.find(`.${CSS.saveBtnWrapper}`);
        btn.hide();
        this.domContainer.find(`.${CSS.scrollFix}`).remove()
    }

    initDOMEvents() {
        let self = this;
        this.domContainer.on('click','[data-form-action="edit"]', function(evt) {
            self.showSaveBtn();
            self.tabBuilder.edit(self.tabBuilder.getActiveIndexOfActiveTab());

            self.domContainer.find('[data-form-action="edit"]').hide();
        });
        this.domContainer.on('click',`.${CSS.saveBtnWrapper} > div`, function (evt) {
            self.hideSaveBtn();
            self.tabBuilder.save();
            self.domContainer.find('[data-form-action="edit"]').show();
            alert(self.saveToJSON());
        });
    }

    getEditBtnHTML() {
        return '<span data-form-action="edit"><i class="far fa-edit"></i></span>';
    }

}
import {FormBuilder} from "./FormBuilder";

import Env from 'bootloader';
import {Uuidv4} from "module/helper/Uuid";
import {IBuilder} from "./IBuilder";
import {BuilderJSON} from "./BuilderJSON";

import CSS from "./css/sectionBuilder.module.scss";
let $ = require('jquery');



export class SectionBuilder extends IBuilder {
    /**
     * @param {IAppContext} ctx
     * @param {jQuery} dom
     */
    constructor(ctx,dom) {
        super(ctx,dom);

        this.isEditable = true;
        /**
         *
         * @type {Section[]}
         */
        this.sections = [];

        /**
         * @param {Section} section
         * @return {string}
         * @private
         */
        this._getSectionHtml = function (uuid,content) {
            return `
<div data-form-section-uuid="${uuid}" class="row">
    <div class="col-1 ${CSS.sectionSortHandle}" style="display:none">
        <div class="${CSS.moveUp}"><i class="fas fa-caret-up"></i></div>
        <div class="${CSS.moveDown}"><i class="fas fa-caret-down"></i></div>
    </div>
    <div class="${CSS.sectionHeader} col">
        <div class="${CSS.sectionTitle} col-md-10">${content}</div>
        <div class="${CSS.sectionControl} col-md-2 text-right">
            <div class="${CSS.editing}" style="display: none">
            <div data-form-action="delete"><i class="far fa-trash-alt"></i></div>
        </div>
<div class="${CSS.display}">
${this.ctx.getEditBtnHTML()}
</div>
</div>
</div><div class="offset-1 col-11 ${CSS.sectionContent}"></div></div>
`;
        };


        this.domContainer.append(`<div class="${CSS.sectionBuilderSectionsContainer}"></div><div class="${CSS.sectionBuilderControl}"><span class="${CSS.addNewSection}" style="display:none;">Add New Section</span></div>`);

        this.sectionsDomContainer = this.domContainer.find(`.${CSS.sectionBuilderSectionsContainer}`);

        this.initDOMEvents();

    }

    /**
     *
     * @param {string} label
     */
    addSection(label) {
        let newSection = new Section();
        newSection.label = label;
        newSection.uuid = Uuidv4();
        this.sectionsDomContainer.append(this._getSectionHtml(newSection.uuid,Section.getDisplayHTML(newSection)));
        let domRef = this.sectionsDomContainer.find(`[data-form-section-uuid="${newSection.uuid}"] > .${CSS.sectionContent}`).first();
        newSection.form = new FormBuilder(this.ctx,domRef);
        this.sections.push(newSection);
        return newSection;
    }

    _addEditSection() {
        this._editSection(this.addSection(''));
    }

    save() {
        for(let i = 0; i < this.sections.length; i++)
        {

            let sectionDom = this.domContainer.find(`[data-form-section-uuid="${this.sections[i].uuid}"]`).first();

            let sectionTitleDom = sectionDom.find(`.${CSS.sectionHeader} > .${CSS.sectionTitle}`).first();
            this.sections[i].label = sectionTitleDom.find('input').first().val();
            sectionTitleDom.html(Section.getDisplayHTML(this.sections[i]));

            sectionDom.find(`.${CSS.display}`).show();
            sectionDom.find(`.${CSS.editing}`).hide();
            this.sections[i].form.save();
        }
        this.domContainer.find(`.${CSS.addNewSection}`).hide();
        this.domContainer.find(`.${CSS.sectionSortHandle}`).hide();

    }

    _editSection(section) {
        let elementField = section;
        let sectionDom = this.domContainer.find(`[data-form-section-uuid="${elementField.uuid}"]`).first();
        sectionDom.find(`.${CSS.sectionHeader} > .${CSS.sectionTitle}`).html(Section.getEditHtml(elementField));

        sectionDom.find(`.${CSS.display}`).hide();
        sectionDom.find(`.${CSS.editing}`).show();

        section.form.edit();
    }

    edit() {
        for(let i = 0; i < this.sections.length; i++)
        {
            this._editSection(this.sections[i]);
        }
        this.domContainer.find(`.${CSS.addNewSection}`).show();
        this.domContainer.find(`.${CSS.sectionSortHandle}`).show();

    }

    initDOMEvents() {
        let self = this;
        this.domContainer.on('click',`.${CSS.addNewSection}`, function (evt) {
            self._addEditSection();
        });
    }

    saveToJSON() {
        let ret = new BuilderJSON(this.constructor.name);
        ret.data = {};
        for(let i = 0; i < this.sections.length; i++)
        {
            let tab = new BuilderJSON(this.sections[i].constructor.name);
            tab.data = this.sections[i].form.saveToJSON();
            ret.data[this.sections[i].getUuid()] = tab;
        }
        return ret;
    }

}
class Section {

    constructor() {
        /**
         * @type {FormBuilder}
         */
        this.form = null;
        this.label = '';
        this.uuid = '';
    }


    /**
     * @param {string} label
     */
    setLabel(label) {
        this.label = label;
    }

    /**
     * @return {FormBuilder}
     */
    getForm() {
        return this.form;
    }

    /**
     * @return {string}
     */
    getLabel() {
        return this.label;
    }

    /**
     * @return {string}
     */
    getUuid() {
        return this.uuid;
    }

    static getEditHtml(section) {
        return '<input class="form-control" value="'+section.label+'">';

    }

    static getDisplayHTML(section) {
        return `<h3>${section.label}</h3>`;
    }

}
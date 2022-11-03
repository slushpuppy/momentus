import {IBuilder} from "./IBuilder";
import CSS from './css/formBuilder.module.scss'

export class Field {
    /**
     * @param {Field|Object|null} obj
     * @param {IAppContext} ctx
     * @returns {Field}
     */
    constructor(ctx, obj) {
        this.ctx = ctx;
        this.uuid = '';
        this.label= '';
        this.required = false,
        this.editOptions = {
            editable: true,
            requirable: true
        }

        this.content = {
            html: '',
            checked: false
        };
        if (obj) {
            for (const property in this)
            {
                if (obj.hasOwnProperty(property))
                {
                    this[property] = obj[property];
                }
            }

        }

    }

    /**
     *
     * @returns {string}
     */
    display ()  {
        return Field.elementFieldBootstrapDisplay(this);
    }

    /**
     *
     * @returns {string}
     */
    edit () {
        return Field.elementFieldBootstrapDisplayEdit(this);
    }

    /**
     * @param {jQuery} formContainer
     */
    initDOMEvents(formContainer) {
        throw new TypeError('Init dom event in child class');
    }

    /**
     *
     * @param {jQuery} dom
     * @return {Field} newly created field element
     */
    updateFromDom(dom) {
        throw new TypeError('update createElementFromDom in child class');
    }



    /**
     *
     * @param {Field} o
     * @returns {string}
     * @constructor
     */
    static elementFieldBootstrapDisplay (o) {
        return `            <div class="${CSS.field}"  data-field-uuid="${o.uuid}"> 

                            <div class="row"> 
                                <div class="col"> 
                                        <div class="${CSS.header}"> 
                                            <div class="${CSS.title}">${o.label}</div> 
                                            <div class="${CSS.rightBlock}"> 
            ${((o.editOptions.editable) ?
                                                `<span class="${CSS.edit}">${o.ctx.getEditBtnHTML()}</span>`
                :``)}
                                            </div> 
             
                                        </div> 
             
                                        <div class="${CSS.content}">${o.content.html}</div> 
                                        <div class="${CSS.footer}"> 
                                            <div class="${CSS.properties}"> 
                                            </div> 
                                            <div class="${CSS.rightBlock}"> 

                                            </div> 
                                        </div> 
                                </div> 
                            </div> 
                        </div>`;

    }

    /**
     *
     * @param {Field} o
     * @returns {string}
     * @constructor
     */
    static elementFieldBootstrapDisplayEdit (o) {
        if (!o.properties) o.properties = '';
        return   `          <div class="${CSS.field}" data-field-uuid="${o.uuid}" data-field-state="editing"> 
                            <div class="row"> 
                                <div class="col-1"> 
                                    <div class="${CSS.moveUp}"><i class="fas fa-caret-up"></i></div> 
                                    <div class="${CSS.moveDown}"><i class="fas fa-caret-down"></i></div> 
                                </div> 
                                <div class="col"> 
                                        <div class="${CSS.header}"> 
                                            <div class="${CSS.title} w-100"><input class="form-control w-100" value="${o.label}" placeholder="Enter Question here..."></div> 
                                            <div class="${CSS.rightBlock}"> 
                                            </div> 
             
                                        </div> 
             
                                        <div class="${CSS.content}"> 
            ${o.content.html}
                                        </div> 
                                        <div class="${CSS.footer}"> 
                                            <div class="${CSS.properties}"> 
                                                <span class="${CSS.delete}"><i class="fas fa-trash-alt"></i></span> 
                                            </div> 
                                            <div class="${CSS.rightBlock}"> 
            ${((o.editOptions.requirable) ? `Required 
                                                    <div class="md-form"> 
                                                        <div class="material-switch"> 
                                                            <input id="required-${o.uuid}" name="switch-primary" type="checkbox" ${((o.required)? `checked="checked"`:``)} > 
                                                            <label for="required-${o.uuid}" class="primary-tint-bg"></label>` : '')}
                                                    </div> 
                                                </div> 
                                            </div> 
                                        </div> 
                                </div> 
                            </div> 
                        </div>`;
    };
    static get EnumType() {
        return '';
    }
    getEnumType() {
        return this.constructor.EnumType;
    }
    saveToJSON()
    {
        let ret = {};
        ret.required = this.required;
        ret.label = this.label;
        return ret;

    }
}





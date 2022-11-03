import {Field} from "../Field";
import CSS from '../css/formBuilder.module.scss'

export class TextInput extends Field {
    /**
     * @param {IAppContext} ctx
     * @param {Field|Object|null} obj
     */
    constructor(ctx,obj) {
        super(ctx,obj);

        this.placeholder =  (obj && obj.hasOwnProperty('placeholder')) ? obj.placeholder : '';

    }


    display() {
        this.content.html =                    '<input data-value="placeholder" class="form-control w-100" placeholder="'+this.placeholder+'">';
        return super.display();
    }
    edit() {
        this.content.html =                    '<input data-value="placeholder" class="form-control w-100" value="'+this.placeholder+'">';
        return super.edit();
    }
    static get EnumType() {
        return 'input';
    }
    initDOMEvents(formContainer)
    {

    }

    /**
     * @param {jQuery} dom
     * @return {TextInput}
     */
    updateFromDom(dom) {
        this.placeholder = dom.find(`.${CSS.content} input`).first().val();

    }

    saveToJSON() {
        let ret = super.saveToJSON();
        ret.label = this.label;
        return ret;
    }
}


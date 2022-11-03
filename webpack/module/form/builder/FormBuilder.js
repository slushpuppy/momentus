import CSS from './css/formBuilder.module.scss';
import {TextInput} from "./fields/TextInput";
import {Select} from "./fields/Select";
import {Field} from "./Field";
import {Uuidv4} from 'module/helper/Uuid';
import Env from 'bootloader';
import {IBuilder} from "./IBuilder";
import {BuilderJSON} from "./BuilderJSON";

let $ = require('jquery');

Env.addEventsCount(2);

(function($) {
    $.fn.invisible = function() {
        return this.each(function() {
            $(this).css("visibility", "hidden");
        });
    };
    $.fn.visible = function() {
        return this.each(function() {
            $(this).css("visibility", "visible");
        });
    };
    var swapArrayElements = function(arr, indexA, indexB) {
        var temp = arr[indexA];
        arr[indexA] = arr[indexB];
        arr[indexB] = temp;
    };
    Array.prototype.swap = function(indexA, indexB) {
        swapArrayElements(this, indexA, indexB);
    };
    /**
     * @param uuid
     * @returns {Select|TextInput}
     */
    Array.prototype.findElementWithUuid= function(uuid) {
        return this[this.findIndexWithUuid(uuid)];
    };
    /**
     * @param uuid
     * @returns {number}
     */
    Array.prototype.findIndexWithUuid = function(uuid) {
        for(var i = 0; i <this.length; i++) {
            if (uuid == this[i].uuid) return i;
        }
    };
}($));
export class FormBuilder extends IBuilder {
    /**
     *
     * @param {IAppContext} ctx
     * @param {jQuery} dom
     */
    constructor(ctx,dom) {
        super(ctx,dom);
        this._NewElementField = null;
        this.isAnimating = false;
        /**
         * @type {TextInput[]|Select[]}
         */
        this.elementFieldList = [];

        this.domContainer.append(`<div class="${CSS.fieldContainer}"></div><div class="btn ${CSS.addField} w-100" style="display: none">Add Field</div>`);
        this._NewElementField = function (uuid) {
            return `            <div class="${CSS.field}"  data-field-uuid="${uuid}">
                                <div class="row">
                                    <div class="col-1">
                                        <div class="${CSS.moveUp}"><i class="fas fa-caret-up"></i></div>
                                        <div class="${CSS.moveDown}"><i class="fas fa-caret-down"></i></div>
                                    </div>
                                    <div class="col">
                                            <div class="${CSS.header}">
                                                <div class="${CSS.rightBlock}">
                                                </div>
                
                                            </div>
                
                                            <div class="${CSS.content}">
                <select class="new-element-selector  form-control">
                <option value="${TextInput.EnumType}">Question To Answer</option> 
                <option value="${Select.EnumType}">Choose Answer from List</option> 
                </select> 
                </div>
                                            <div class="${CSS.footer}">
                                                <div class="${CSS.properties}">
                                                     <span class="btn btn-primary-tint new-field-next">Next</span><span class="${CSS.delete}"><i class="far fa-trash-alt"></i></span>
                                                </div>
                                                <div class="${CSS.rightBlock}">
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>`;
        }
        this.initDOMEvents();
    }


    save() {
        for (let i = 0; i < this.elementFieldList.length; i++)
        {
            let elementField = this.elementFieldList[i];
            let parent = this.domContainer.find('[data-field-uuid="'+this.elementFieldList[i].uuid+'"]').first();

            if (elementField.constructor.name === Field.name)
            {
                this.elementFieldList.splice(i,1);
                i--;
                parent.remove();
                continue;
            }
            let uuid = elementField.uuid;
            elementField.label = parent.find(`.${CSS.title} input`).first().val();
            elementField.required = parent.find('#required-'+ uuid).first().prop("checked");
            elementField.updateFromDom(parent);
            parent.replaceWith(elementField.display());
            this.domContainer.find('[data-field-uuid="'+uuid+'"] [data-toggle="toggle"]').bootstrapToggle();
        }
        this.domContainer.find(`.${CSS.addField}`).first().hide();

    }


    loadFromJSON () {
        this.elementFieldList = [];
        let tempList;
        if (typeof str == "string")
        {
            tempList = JSON.parse(str).fields;
        }
        else {
            if (str.fields && str.fields.length > 0 && typeof str.fields[0].uuid == "string") {
                tempList = str.fields;
            }
        }
        var html = '';
        if (tempList.length > 0) {
            for (var i = 0; i < tempList.length; i++) {
                let element;
                switch (tempList[i].type)
                {
                    case TextInput.EnumType:
                        element = new TextInput(tempList[i]);
                        break;
                    case Select.EnumType:
                        element = new Select(tempList[i]);
                        break;
                    case 'autocomplete':
                        break;
                }
                if (element) {
                    html += element.display();
                    element.initDOMEvents(self.domContainer);
                }
                this.elementFieldList[i] = element;

            }
            self.domContainer.children('.fields').first().append(html);
            self.domContainer.find('[data-toggle="toggle"]').bootstrapToggle();
            self.refreshSortHandles();
        }
    }


    initDOMEvents() {
        let self = this;

        this.domContainer.on('click', '.new-field-next', function () {
            let parent = $(this).parents(`.${CSS.field}`).first();
            let uuid = parent.attr('data-field-uuid');
            let elementFieldIdx = self.elementFieldList.findIndexWithUuid(uuid);

            let selector = parent.find('.new-element-selector').first().val();
            let replacementField;
            switch (selector) {
                case TextInput.EnumType:
                    replacementField = new TextInput(self.ctx,{uuid: uuid});

                    break;
                case Select.EnumType:
                    replacementField = new Select(self.ctx,{uuid: uuid});
                    break;
                case 'autocomplete':
                    break;
            }
            self.elementFieldList[elementFieldIdx] = replacementField;
            replacementField.initDOMEvents(self.domContainer);
            parent.replaceWith(replacementField.edit());
            self.domContainer.find('[data-field-uuid="' + uuid + '"] [data-toggle="toggle"]').bootstrapToggle();
            self.refreshSortHandles();
        });


        this.domContainer.on('click', `.${CSS.delete}`, function () {
            let parent = $(this).parents(`.${CSS.field}`).first();
            let uuid = parent.attr('data-field-uuid');
            let idx = self.elementFieldList.findIndexWithUuid(uuid);
            self.elementFieldList.splice(idx,1);
            parent.remove();

            self.refreshSortHandles();
        });
        this.domContainer.on('click','[name="switch-primary"]', function () {
            let parent = $(this).parents('.form-field').first();
            let uuid = parent.attr('data-field-uuid');
            let elementField = self.elementFieldList.findElementWithUuid(uuid);
            elementField.is_required = $(this).prop("checked");
        });


        this.domContainer.on('click', `.${CSS.moveUp}`, function () {
            sortFunc($(this), '.move-up');

        });
        this.domContainer.on('click', `.${CSS.moveDown}`, function () {
            sortFunc($(this), '.move-down');
        });

        let sortFunc = function (ctx, sortType) {

            if (self.isAnimating) {
                return;
            }

            let clickedDiv = ctx.parents(`.${CSS.field}`).first(),
                prevDiv = null,
                distance = clickedDiv.outerHeight();

            if (sortType == '.move-up') {
                prevDiv = clickedDiv.prev();
            } else {
                let tempDiv = clickedDiv.next();
                prevDiv = clickedDiv;
                clickedDiv = tempDiv;
            }

            if (prevDiv && prevDiv.length) {
                self.isAnimating = true;
                $.when(clickedDiv.animate({
                        top: -prevDiv.outerHeight()
                    }, 600),
                    prevDiv.animate({
                        top: clickedDiv.outerHeight()
                    }, 600)).done(function () {
                    prevDiv.css('top', '0px');
                    clickedDiv.css('top', '0px');
                    clickedDiv.insertBefore(prevDiv);
                    self.isAnimating = false;
                    self.refreshSortHandles();
                });
            }

        };

        this.domContainer.on('click', `.${CSS.addField}`, function (evt) {

            let uuid = Uuidv4();
            let tmp = new Field(self.ctx, null);
            tmp.uuid = uuid;
            self.elementFieldList.push(
                tmp
            );
            self.domContainer.children(`.${CSS.fieldContainer}`).first().append(self._NewElementField(uuid));
            self.refreshSortHandles();
        });
        Env.eventComplete();
    }
    
    refreshSortHandles () {
        let self = this;
        function disableBtn (btn) {
            btn.removeClass('primary-tint');
            btn.addClass('disabled-tint');
        }
        function enableBtn(btn) {
            btn.addClass('primary-tint');
            btn.removeClass('disabled-tint');
        }
        self.domContainer.find(`.${CSS.moveDown},.${CSS.moveUp}`).each(function(){
            enableBtn($(this));
        });
        disableBtn(self.domContainer.find(`.${CSS.moveUp}`).first());
        disableBtn(self.domContainer.find(`.${CSS.moveDown}`).last());


    }

    edit() {


        for (let i = 0; i < this.elementFieldList.length; i++)
        {
            let elementField = this.elementFieldList[i];

            let parent = this.domContainer.find('[data-field-uuid="'+this.elementFieldList[i].uuid+'"]').first();
            let uuid = elementField.uuid;

            let cnode = parent.replaceWith(elementField.edit());
            this.domContainer.find('[data-field-uuid="'+uuid+'"] [data-toggle="toggle"]').bootstrapToggle();
            this.refreshSortHandles();
        }

        this.domContainer.find(`.${CSS.addField}`).first().show();


    }
    saveToJSON() {
        let ret = new BuilderJSON(this.constructor.name);
        ret.data = {};
        for(let i = 0; i < this.elementFieldList.length; i++)
        {
            let elementField = this.elementFieldList[i];
            let tab = new BuilderJSON(elementField.constructor.name);
            tab.data = elementField.saveToJSON();
            ret.data[elementField.uuid] = tab;
        }
        return ret;

    }
}



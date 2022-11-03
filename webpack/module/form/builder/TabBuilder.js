import {FormBuilder} from "./FormBuilder";

import Env from 'bootloader';
import {Uuidv4} from "module/helper/Uuid";
import {IBuilder} from "./IBuilder";
import {SectionBuilder} from "./SectionBuilder";
import CSS from "./css/tabBuilder.module.scss";
import {BuilderJSON} from "./BuilderJSON";
let $ = require('jquery');

export class TabBuilder extends IBuilder {
    /**
     *
     * @param {IAppContext} ctx
     * @param {jQuery} dom
     */
    constructor(ctx,dom) {
        super(ctx,dom);
        this.domContainer = dom;
        this.uuid = Uuidv4();
        /**
         *
         * @type {Tab[]}
         */
        this.tabs = [];


        this.domContainer.append(
            `<div class="module-form" data-module-form-uuid="this.uuid"> 
                <div class="panel-heading"> 
                   <ul class="nav nav-tabs ${CSS.tabNav}"> 
                  </ul>      
                </div> 
                <div class="panel-body"> 
                  <div class="tab-content"> 
                  </div> 
                </div> 
              </div>`
        );
        /**
         * @param {Tab} tab
         */
        this._getTabRowHtml = function (tab) {
            return '<li class="'+CSS.tab+'" data-form-tab-uuid="'+tab.uuid+'"></li>'
        };
        /**
         * @param {Tab} tab
         */
        this._getContentRowHtml = function (tab) {
            return             '<div class="tab-pane" data-form-tab-pane-uuid="'+tab.uuid+'"></div>';
        }
        this.tabDom = this.domContainer.find('.panel-heading > .nav').first();
        this.tabContentDom = this.domContainer.find('.panel-body > .tab-content').first();
        this.initDOMEvents();
    }

    /**
     * @param {string} label
     * @private
     */
    _addTab(label) {
        let self = this;
        let ret = {
            newTab: new Tab(),
            domRef: null
        };
        ret.newTab.label = label;
        ret.newTab.uuid = Uuidv4();
        this.tabDom.append(
            self._getTabRowHtml(ret.newTab)
        );
        let currentTab = this.tabDom.children('[data-form-tab-uuid="'+ret.newTab.uuid+'"]').first();

        currentTab.html(Tab.getDisplayHTML(ret.newTab));


        this.tabContentDom.append(
            self._getContentRowHtml(ret.newTab)
        );

        ret.domRef = this.tabContentDom.children('[data-form-tab-pane-uuid="'+ret.newTab.uuid+'"]').first();
        this.tabs.push(ret.newTab);
        if (this.tabs.length == 1)
        {
            this.tabDom.find('li > span').first().click();
        }
        return ret;
    }

    /**
     *
     * @param {string} uuid
     * @return Tab
     */
    loadTabWithUuid(uuid) {
        for (let i = 0; i < this.tabs.length;i++)
        {
            if (this.tabs[i].uuid == uuid)
                return this.tabs[i];
        }
    }

    /**
     *
     * @param {string} label
     * @param  content callback with DOM parameter
     */
    addTabForm(label) {
        let obj = this._addTab(label);
        obj.newTab.content = new FormBuilder(this.ctx,obj.domRef);
        return obj.newTab.uuid;
    }

    /**
     *
     * @param {string} label
     * @param  content callback with DOM parameter
     * @return string
     */
    addTabSection(label) {
        let obj = this._addTab(label);
        obj.newTab.content = new SectionBuilder(this.ctx,obj.domRef);
        return obj.newTab.uuid;
    }

    initDOMEvents() {
        let self = this;
        self.tabDom.on('click', `li:not(.${CSS.disabled}) > span`, function(e){
            let tab  = $(this).parent(),
             tabIndex = tab.index(),
             tabPanel = self.tabContentDom,
             tabPane = tabPanel.find('.tab-pane').eq(tabIndex);
            tabPanel.find('.active').removeClass('active');
            self.tabDom.find('.active').removeClass('active');
            tab.addClass('active');
            tabPane.addClass('active');
        });
        self.tabDom.on('click','[data-form-tab-new] > i',function (e) {
            e.stopImmediatePropagation();
            self.addTabSection('New Section');
        });
    }

    save(index) {
        let tabSrc = this.tabs;
        this.tabDom.find('li').removeClass(CSS.disabled);

        if (Number.isInteger(index))
        {
            tabSrc = [
                this.tabs[index]
            ];
        }


        for(let i = 0; i < tabSrc.length; i++) {
            let parent = this.domContainer.find('[data-form-tab-uuid="'+tabSrc[i].uuid+'"]').first();
            let input = parent.find('input').first();
            if (input.length > 0)
            {
                tabSrc[i].label = input.val();
                parent.html(Tab.getDisplayHTML(tabSrc[i]));

                let content = tabSrc[i].getContent();
                if (content.isEditable) {
                    content.save();
                }
            }

        }
        this.tabDom.find('[data-form-tab-new]').remove();
    }

    edit(index) {
        let tabSrc = this.tabs;
        this.tabDom.find('li').addClass(CSS.disabled);
        if (Number.isInteger(index))
        {
            tabSrc = [
                this.tabs[index]
            ];
        }
        for(let i = 0; i < tabSrc.length; i++) {
            let parent = this.domContainer.find('[data-form-tab-uuid="'+tabSrc[i].uuid+'"]').first();
            parent.html(Tab.getEditHtml(tabSrc[i]));

            let content = tabSrc[i].getContent();
            if (content.isEditable) {
                content.edit();
            }
            parent.removeClass(CSS.disabled);


        }
        if (this.tabDom.find('[data-form-tab-new]').length == 0)
        this.tabDom.append(`<li class="${CSS.tab}" data-form-tab-new=""><i class="fas fa-plus"></i></li>`);

    }

    getActiveIndexOfActiveTab() {
        return this.tabDom.find('li.active').index();
    }

    saveToJSON() {
        let ret = new BuilderJSON(this.constructor.name);
        ret.data = {};
        for(let i = 0; i < this.tabs.length; i++)
        {
            let tab = new BuilderJSON(this.tabs[i].content.constructor.name);
            tab.data = this.tabs[i].content.saveToJSON();
            ret.data[this.tabs[i].uuid] = tab;
        }
        return ret;

    }

}


class Tab {

    constructor() {
        /**
         * @type {FormBuilder|SectionBuilder}
         */
        this.content = null;
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
     * @return {string}
     */
    getLabel() {
        return this.label;
    }

    /**
     * @return {FormBuilder|SectionBuilder}
     */
    getContent() {
        return this.content;
    }

    /**
     * @return {string}
     */
    getUuid() {
        return this.uuid;
    }

    static getEditHtml(tab) {
        return '<input class="form-control" value="'+tab.label+'">';

    }

    static getDisplayHTML(tab) {
        return '<span class="cursor-pointer">'+tab.label+'</span>';
    }

}
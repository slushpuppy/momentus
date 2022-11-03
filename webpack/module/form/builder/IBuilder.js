export class IBuilder {
    /**
     * @param {jQuery} dom
     * @param {IAppContext} ctx
     */
    constructor(ctx,dom) {
        this.domContainer = dom;
        this.ctx = ctx;
    }



    loadFromJSON () {
        throw new Error('Declare loadfromjson in child class');
    }

    saveToJSON() {
        throw new Error('Declare saveAsJSON in child class');

    }

    initDOMEvents() {
        throw new Error('Declare initEvents in child class');

    }

    /**
     * @type int|null
     * @param index
     */
    edit(index) {
        throw new Error('Declare switchToEditMode in child class');
    }

    /**
     * @type int|null
     * @param index
     */
    save(index) {
        throw new Error('Declare switchToDisplayMode in child class');
    }


}
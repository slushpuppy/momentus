export class ViewController
{
    /**
     *
     * @param {SinglePageApp} spa
     */
    constructor() {
        /**
         * @type {AbstractView[]}
         */
        this.routes = [];

        /**
         *
         * @type {AbstractView[]}
         */
        this.currentRoute = [];


    }

    /**
     * @param {JsonResponse} data
     */
    onOffline()
    {
        for(let i = 0;i < this.currentRoute.length;i++)
        {
            this.currentRoute[i].onOffline();
        }
    }


    /**
     *
     * @param {JsonResponse} jsonData
     */
    run(jsonData) {
        this.unloadCurrentRoutes();
        for(let i = 0;i < this.routes.length;i++)
        {
            if (this.routes[i].isValid(jsonData))
            {
                this.routes[i].init(jsonData);
                this.routes[i].onPageLoad();

                this.routes[i].run();
                this.currentRoute.push(this.routes[i]);
            }
        }
    }

    unloadCurrentRoutes()
    {
        for(let i =0;i< this.currentRoute.length; i++)
        {
            this.routes[i].onPageUnload();
        }
        this.currentRoute = [];
    }

    /**
     *
     * @param {AbstractView} view
     */
    addRoute(view) {
        this.routes.push(view);
    }
}
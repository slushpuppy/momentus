class Environment {
    constructor(){
        /**
         * @type {number}
         */
        this.eventsToLoad = 0;
    }

    /**
     * @param {number} i
     */
    addEventsCount(i){
        this.eventsToLoad += i;
    }
    eventComplete() {
        this.eventsToLoad--;
    }

    /**
     * @return {number}
     */
    getEventsLeft() {
        return this.eventsToLoad;
    }

    isMobileBrowser() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i.test(navigator.userAgent);
    }

    /**
     *
     * @param {string} module
     * @param msg
     */
    debugLog(module,msg)
    {
        if (process.env.NODE_ENV === 'development')
        {
            console.log(`[${module}] ${msg}`);
        }
    }

    /**
     *
     * @param {string} module
     * @param msg
     */
    log(module,msg)
    {
        console.log(`[${module}] msg`);

    }
}


const Env = new Environment();
Object.seal(Env);
export default Env;

(function () {



} ());
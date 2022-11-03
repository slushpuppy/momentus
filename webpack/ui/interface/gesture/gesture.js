import Env from 'bootloader';

export class GestureEvent {

    constructor(callback,eventType,direction) {
        this.callback = callback;
        this.type = eventType;
        this.direction = direction || GestureEvent.DIRECTION_ALL ;
    }

    static get RETURN_NEXT() {
        return true;
    }

    static get RETURN_HALT()
    {
        return false;
    }

    static get EVENT_TAP() {
        return 'tap';
    }
    static get EVENT_DRAG() {
        return 'drag';
    }
    static get DIRECTION_VERTICAL() {
        return 'vertical';
    }

    static get DIRECTION_HORIZONTAL() {
        return 'horizontal';
    }
    static get DIRECTION_ALL() {
        return 'all';
    }

}
Object.freeze(GestureEvent);
export default class Gesture {

    static get EVENT_TAP_SINGLE_MAX_TIME()
    {
        return 300;
    }
    static get EVENT_TAP_SINGLE_MAX_DISPLACEMENT_PX()
    {
        return 20;
    }

    constructor(targetDom) {
        this.targetDom = targetDom;

        this.track = {mouse:{},touch:{}};


        /**
         *
         * @type {GestureEvent[]}
         */
        this.events = [];



        this.targetDom.addEventListener('mousedown', e => {

            this.track.mouse = {
                isMouseDown: true,
                touchDownStartMS: this._getTimeMilliSeconds(),
                pos: {
                    initialX: e.offsetX,
                    initialY: e.offsetY,
                    deltaX: 0,
                    deltaY: 0
                },

            };

        });

        this.targetDom.addEventListener('touchstart', e => {


            e.preventDefault();

            switch (e.touches.length)
            {
                case 1:
                    if (!this._isDomTarget(e.touches[0])) return;
                    let touchEvt = this.getTouchOffset(e.touches[0]);
                    if (touchEvt != null)
                    {
                        this.track.touch.single = {
                            isTouchDown: true,
                            touchDownStartMS: this._getTimeMilliSeconds(),
                            pos: {
                                initialX: touchEvt.offsetX,
                                initialY: touchEvt.offsetY,
                                deltaX: 0,
                                deltaY: 0
                            },
                        };
                    }

                    break;
            }

        });


        this.targetDom.addEventListener('mouseup', e => {

            let eventToTrigger = [];
            let e1 = {};
            if (this.track.mouse.isMouseDown) {
                if ((this._getTimeMilliSeconds() - this.track.mouse.touchDownStartMS) < Gesture.EVENT_TAP_SINGLE_MAX_TIME && ((this.track.mouse.pos.deltaY + this.track.mouse.pos.deltaX) < Gesture.EVENT_TAP_SINGLE_MAX_DISPLACEMENT_PX)) {
                    eventToTrigger.push(GestureEvent.EVENT_TAP);
                }
            }
            this.track.mouse.isMouseDown = false;
            this._triggerEvent(eventToTrigger,e1);
        });
        this.targetDom.addEventListener('mouseleave', e => {
            if (!this._isDomTarget(e)) return;

            this.track.mouse.isMouseDown = false;
        });


        let touchEnd = ()=> {
            this.track.touch.single.isTouchDown = false;
        };
        this.targetDom.addEventListener('touchend', e => {

            e.preventDefault();

            let eventToTrigger = [];
            let e1 = {};
            if (this.track.touch.single.isTouchDown) {
                if ((this._getTimeMilliSeconds() - this.track.touch.single.touchDownStartMS) < Gesture.EVENT_TAP_SINGLE_MAX_TIME && ((this.track.touch.single.pos.deltaY + this.track.touch.single.pos.deltaX) < Gesture.EVENT_TAP_SINGLE_MAX_DISPLACEMENT_PX)) {
                    eventToTrigger.push(GestureEvent.EVENT_TAP);
                }
            }

            this._triggerEvent(eventToTrigger,e1);
            touchEnd();
        });
        this.targetDom.addEventListener('touchcancel', e => {
            e.preventDefault();
            touchEnd();
        });


        let triggerMoveEvent = (deltaX,deltaY,calcDirection) => {

            let e1 = {deltaX: deltaX,deltaY: deltaY};
            this._triggerEvent([GestureEvent.EVENT_DRAG],e1, (event) => {

                switch(event.direction) {
                    case GestureEvent.DIRECTION_ALL:
                        return true;
                        break;
                    case GestureEvent.DIRECTION_HORIZONTAL:
                        return calcDirection  > 1.0;
                        break;
                    case GestureEvent.DIRECTION_VERTICAL:
                        return calcDirection < 1.0;
                        break;
                    default:
                        return false;
                }

            });
        };
        this.targetDom.addEventListener('mousemove', e => {

            if (this.track.mouse.isMouseDown)
            {

                let deltaX = e.offsetX - this.track.mouse.pos.initialX,deltaY = e.offsetY - this.track.mouse.pos.initialY;
                let calcDirection = deltaX / deltaY;
                this.track.mouse.pos.deltaX += deltaX;
                this.track.mouse.pos.deltaY += deltaY;
                triggerMoveEvent(deltaX,deltaY,calcDirection);
            }
        });


        this.targetDom.addEventListener('touchmove', e => {
            e.preventDefault();

            switch (e.touches.length)
            {
                case 1:
                    if (this.track.touch.single.isTouchDown) {
                        let touchEvt = this.getTouchOffset(e.touches[0]);
                        if (touchEvt != null)
                        {
                            let deltaX = touchEvt.offsetX - this.track.touch.single.pos.initialX,deltaY = touchEvt.offsetY - this.track.touch.single.pos.initialY;
                            this.track.touch.single.pos.deltaX += Math.abs(deltaX);
                            this.track.touch.single.pos.deltaY += Math.abs(deltaY);
                            Env.debugLog('touchmove single', `XDelta: ${this.track.touch.single.pos.deltaX} YDelta: ${this.track.touch.single.pos.deltaY}`)
                            let calcDirection = deltaX / deltaY;
                            triggerMoveEvent(deltaX,deltaY,calcDirection);
                        }
                    }

                    break;
            }

        });
    }

    _triggerEvent(eventsToTrigger,e,customCondition)
    {
        for (let i = 0; i < this.events.length;i++)
        {
            let event = this.events[i];
            if (eventsToTrigger.includes(event.type) && (typeof customCondition === "undefined" || customCondition === null || customCondition(event)))
            {
                Env.debugLog(event.type,'_triggerEvent');
                let ret = event.callback(e);
                if (ret == GestureEvent.RETURN_HALT) break;
            }
        }
    }


    getTouchOffset(touch)
    {

        let realTarget = this.targetDom;
        let evt = {};
        if (realTarget) {
            evt.offsetX = touch.clientX-realTarget.getBoundingClientRect().x;
            evt.offsetY = touch.clientY-realTarget.getBoundingClientRect().y;
        } else {
            return null;
        }
        return evt;
    }

    /**
     *
     * @param {GestureEvent} event
     */
    addEvent(event)
    {
        this.events.push(event);
    }

    _getTimeMilliSeconds()
    {
        return (new Date()).getTime();
    }

    _isDomTarget(e)
    {
        return e.target == this.targetDom;
    }

}

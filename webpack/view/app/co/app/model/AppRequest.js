import {HttpRequest} from "lib/http/core/HttpRequest";

export class AppRequest extends HttpRequest
{
    constructor() {
        super();
        this.app = null;
    }
}
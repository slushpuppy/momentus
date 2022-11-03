
export class AppJsonBodyRequest
{
    constructor() {
        this.loadAll = false;
    }
    toJsonString()
    {
        return JSON.stringify(this);

    }
}
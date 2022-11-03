export class Http {
    static JSONStrToObject(jsonStr,destObj)
    {
        try {
            var o = JSON.parse(jsonStr);

            if (o && typeof o === "object") {
                if (destObj === null) return o;
                return Object.assign(new this(),o);
            }
        }
        catch (e) { }
        return null;
    }


}
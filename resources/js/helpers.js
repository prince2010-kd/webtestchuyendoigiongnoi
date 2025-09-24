function waitForJquery(callback)
{
    if(window.$ && window.$.fn)
    {
        callback();
    }else{
        setTimeout(() => waitForJquery(callback), 50);
    }
}
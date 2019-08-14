var nrServer = 'https://my.nanorep.com';

    // show/hide article text
function toggleArticle(id, account, kb, _event, el)
{
    // ignore event if it didnt originate from span 
    var e = _event || window.event;
    var target = e.target || e.srcElement;
    if (target.parentNode != el && target.parentNode.parentNode != el) return;

    var article = document.getElementById('article_' + id);
    if (article.style.display == 'block')
    {
        el.className = 'nR_title_cl';
        article.style.display = 'none';
    }
    else
    {
        el.className = 'nR_title_op';
        article.style.display = 'block';

        // request img
        if (!el.statsRequested)
        {
            var url = new Array();
            url.push(nrServer);
            url.push('/~');
            url.push(account);
            url.push('/common/API/kbExportAnswer2.gif?rnd=');
            url.push(Math.floor(Math.random() * 1000000));
            url.push('&a=');
            url.push(id);
            url.push('&account=');
            url.push(account);
            url.push('&kb=');
            url.push(kb);

            downloadImage(url.join(''));
            //document.body.appendChild(img);

            el.statsRequested = true;
          }
    }
}
function downloadImage(url)
{
    var img = new Image();
    img.src = url;
    img.style.width = '1px';
    img.style.height = '1px';
    img.style.display = 'none';
}
function onloadFnc(url)
{
    downloadImage(nrServer + '/common/api/statshello.gif');
}
;(function(){
    const p = new URLSearchParams(window.location.search);
    const key = p.get('coex_msg');
    if (!key) return;

    fetch(DSProfileData.ajaxUrl, {
        method:'POST',
        credentials:'same-origin',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: `action=wv_coex_flash&security=${DSProfileData.nonce}&key=${encodeURIComponent(key)}`
    })
    .then(r=>r.json()).then(res=>{
        if(res.success && res.data){
            alert(res.data);                 // swap for toast if desired
        }
        p.delete('coex_msg');
        history.replaceState({},'',location.pathname + (p.toString()?('?'+p.toString()):''));
    });
})();

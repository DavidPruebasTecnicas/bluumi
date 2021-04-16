<?php
echo `<button onClick='aaa()'>aaa</button>
<script>

    function aaa()
    {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: { name: 'aaaaa', cif: 'A12345678', description: 'a', logo: 'aaa', email: 'aaaaa@aaa.com' , date:'2020-02-02'  },
            url: "http://beta.farmacia-cm.es/farmaciacm/Borrar/create",
            beforeSend: function(data)
            {
            },
            success: function(data) {
            },
            error: function()
            {
            },
            complete: function()
            {
            },
        });
    }
</script>`;
echo 'aaaa';
?>

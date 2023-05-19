$('#doc').pdfFlipbook({ key: '761d28a1a0ce5414' });
    const InputElement = document.getElementById('upload_carta');
        const pond  = FilePond.create(InputElement, {
            onaddfile:(err, file) =>{
                console.log(err, file.getMetadata())
            },
            onprocessfile : (err, file) => {
               if(file.serverId == '1'){
                    Swal.fire({
                        title: 'Notificación',
                        html: 'Cartilla subida correctamente',
                        backdrop: "#82fa41"
                    }).then(()=>{
                        window.location.reload();
                    })
               }else{
                Swal.fire({
                    title: 'Notificación',
                    html: file.serverId,
                    backdrop: "#ff000d"
                }).then(()=>{
                    window.location.reload();
                })
               }
            }
        });
        $('#upload_carta').on('FilePond:processfile', function(e) {
            
        });
        FilePond.setOptions ({
            server: {
                url: $("#url").val() + "carta/sube_carta",
            },
            maxFiles: 1,
            required: true,
        })
      
        var urlComplete = $("#route").val()
        var doc = $("#doc").val()
          document.addEventListener("adobe_dc_view_sdk.ready", function()
          {
              var adobeDCView = new AdobeDC.View({clientId: "2a95528a704b4aa2bf6a5ce4237455b3", divId: "adobe-dc-view"});
              adobeDCView.previewFile(
            {
                content:   {location: {url: urlComplete }},
                metaData: {fileName: doc}
            });
          });

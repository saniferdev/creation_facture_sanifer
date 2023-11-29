<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="GESTION DE FACTURATION">
    <meta name="author" content="Winny">
    <meta name="theme-color" content="#3e454c">
    <link rel="icon" href="./assets/images/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="./assets/images/favicon.ico" type="image/x-icon"/>
    <title>REIMPRESSION DE FACTURE</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="./assets/css/styles.css" rel="stylesheet" id="bootstrap-css">
</head>

<body>
<div class="container register">
                <div class="row">
                    <div class="col-md-3 register-left">
                        <img src="./assets/images/sanifer_logo.jpg" alt=""/>
                        <h3>REIMPRESSION  <br> DE <br> FACTURE</h3>
                    </div>
                    <div class="col-md-9 register-right">
                        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Recherche</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Impression</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <h3 class="register-heading">Recherche Ticket ou Facture</h3>
                                <div class="row register-form">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" id="ticket" class="form-control" placeholder="N° Ticket ou Facture" value="" />
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="loading">
                                      <img id="loading-image" width="50%" src="./assets/images/chargement.gif" alt="Chargement..." />
                                    </div>                                

                                        <div class="col-md-6 resultat">
                                            <div class="form-group">
                                                <label for="N°">N°</label>
                                                <input type="text" class="form-control" id="num_facture" readonly/>
                                            </div>
                                            <div class="form-group">
                                                <label for="Date de la facture">Date de la facture</label>
                                                <input type="text" class="form-control" id="date_facture" readonly/>
                                            </div>
                                            <div class="form-group">
                                                <label for="Ref Client">N° Client</label>
                                                <input type="text" class="form-control" id="num_client_facture" readonly/>
                                            </div>
                                            <div class="form-group">
                                                <label for="Adresse">Adresse</label>
                                                <input type="text" class="form-control" id="adr_client_facture" readonly/>
                                            </div>
                                        </div>
                                        <div class="col-md-6 resultat">
                                            <div class="form-group">
                                                <label for="Prénom">Prénom</label>
                                                <input type="text" class="form-control" id="prenom_client" readonly/>
                                            </div>
                                            <div class="form-group">
                                                <label for="Nom">Nom</label>
                                                <input type="text" class="form-control" id="nom_client" readonly/>
                                            </div>
                                            <div class="form-group">
                                                <label for="Tel">Tel</label>
                                                <input type="email" class="form-control" id="tel_client" readonly/>
                                            </div>
                                        </div>

                                    <div class="col-md-12">
                                        <input type="submit" id="rechercher" class="btnRegister"  value="Rechercher"/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="SANIFER" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content rounded-0">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="popupid">Alerte</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true"><span class="icon-close2"></span></span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    <p style="text-align: center; color: #0062cc;">La facture ou le ticket est inexistante !!!</p>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade show" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <h3  class="register-heading">Ajout information et Impression</h3>
                                <form action="./tcpdf/sanifer/impression.php" method="post" >
                                    <div class="row register-form">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="pre" placeholder="Prénom" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="nom" placeholder="Nom" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="email" class="form-control" name="mail" placeholder="Email" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="phone" class="form-control" placeholder="Tel" value="" />
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="adresse" placeholder="Adresse" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="nif" placeholder="NIF" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="stat" placeholder="STAT" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" name="num" id="num_ticket"/>
                                            </div>
                                            <input class="btn btn-primary" type="reset" id="annuler" value="Annuler"/>
                                            <input type="submit" id="imprimer" class="btn btn-success"  value="Imprimer"/>
                                        </div>
                                        <div class="col-md-12" id="loading2">
                                          <img id="loading-image2" width="50%" src="./assets/images/chargement.gif" alt="Chargement..." />
                                        </div> 

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
</body>
<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/bootstrap.min.js"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $("#ticket").keypress(function(event) {
            if (event.which == 13) {
                gdf.Recherche();
            }       
        });

        $('#rechercher').click(function () {        
            gdf.Recherche();                    
        });

        $('#profile-tab').click(function () {        
            var num = $("#num_facture").val();
            if(num == '') return false;
        });

        var gdf = {}; 
        gdf.Recherche=function() { 
            $('#ticket').each(function() {
               if ($(this).val() != "" && $.trim( $(this).val() ).length > 12) {
                    $("#loading-image").show();
                    $.ajax({
                      method: "POST",
                      url: "resultat.php",
                      dataType: 'JSON',
                      data: { num: $(this).val() }

                    }).done(function(resultat){
                        if(resultat == ""){
                            $("#loading-image").hide();
                            $('#popup').modal('show');
                        }
                        else{
                            Object.keys(resultat[0]).forEach(key => {
                                const dateTime  = resultat[0]["created_date"];
                                const parts     = dateTime.split(/[- :]/);
                                const wanted    = `${parts[2]}/${parts[1]}/${parts[0]} ${parts[3]}:${parts[4]}:${parts[5]}`;
      
                                $("#num_facture").val(resultat[0]["receipt_number"]);
                                $("#num_ticket").val(resultat[0]["receipt_number"]);
                                $("#date_facture").val(wanted);
                                $("#num_client_facture").val(resultat[0]["orderby_code"]);
                                $("#adr_client_facture").val(resultat[0]["orderby_adr1"]);
                                $("#prenom_client").val(resultat[0]["orderby_first_name"]);
                                $("#nom_client").val(resultat[0]["orderby_last_name"]);
                                $("#tel_client").val(resultat[0]["orderby_phone"]);

                            });
                            $("#loading-image").hide();
                            $(".resultat").show();
                        }
                    });
               }
               else return false;
            });
            
        }

    });
</script>
</html>
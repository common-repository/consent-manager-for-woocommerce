<div class="row bs-wizard steps" style="border-bottom:0;">

    <div class="col-lg-4 col-xs-4 bs-wizard-step active">
        <div class="text-center bs-wizard-stepnum">1</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center">Adult Details</div>
    </div>

    <div class="col-lg-4 col-xs-4 bs-wizard-step disabled"><!-- complete -->
        <div class="text-center bs-wizard-stepnum">2</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center">In Case of Emergency</div>
    </div>

    <div class="col-lg-4 col-xs-4 bs-wizard-step disabled"><!-- complete -->
        <div class="text-center bs-wizard-stepnum">3</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center">Consent</div>
    </div>

</div>

<form method="post" enctype="multipart/form-data">
<div class="form-container">


    <div class="step" id="step_1">
        <?php include 'info-form.php'; ?>

    </div>

    <div class="step" id="step_2">
        <?php include 'emergncy-form.php'; ?>

    </div>
    <div class="step" id="step_3">

        <?php include 'agree-form.php'; ?>
    </div>


</div>

    <input type="hidden" name="route_name" value="parent_info"/>
</form>

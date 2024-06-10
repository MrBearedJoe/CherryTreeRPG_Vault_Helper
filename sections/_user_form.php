<div class="col">
  <div class="card p-0 border border-danger bg-dark text-white">
    <h6 class="card-header bg-danger text-white py-1">
      Pull Codes for <?= $login_as ?>
      <?= $lastCodesPulledBtn_user ?>
    </h6>
    <div class="card-body p-2">
      <form action="?user" method="POST">
        <div class="btn-group mb-1" role="group">
          <input type="radio" class='btn-check' name="pullHow" value="fromTop" id='fromTop' autocomplete="off"
            checked />
          <label class="btn btn-sm  btn-outline-light" for='fromTop'>From Top?</label>
          <input type="radio" class='btn-check' name="pullHow" value="yes" id="fromBottom" autocomplete="off" />
          <label class="btn btn-sm  btn-outline-light" for="fromBottom">From Bottom?</label>
          <input type="radio" class='btn-check' name="pullHow" value="random" id='random' autocomplete="off" />
          <label class="btn btn-sm  btn-outline-light" for='random'>Random Spots?</label>
        </div>
        <div class="input-group input-group-sm">
          <input class="form-control form-control-sm mb-1" type="hidden" name="creditAdd" value="creditAdd" />
          <input class="form-control form-control-sm mb-1" type="hidden" name="creditTo" value='<?= $login_as ?>' />
          <input class="form-control form-control-sm mb-1" type="number" step="1" name="numberOfCodes"
            placeholder="How many?" required />
          <button class="btn btn-danger" type="submit">
            Get Codes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
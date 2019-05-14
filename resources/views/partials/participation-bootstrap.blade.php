<div id="utcApp">

    <participation-form inline-template action="/api/participations">
        <form id="utcParticipation" method="post" action="/api/participations"
              @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)" autocomplete="off">

            <div class="row justify-content-center codeField">
                <div class="col-12 text-center pb-5 pt-5">
                    <div class="d-inline-block pb-3 pt-3 pb-md-5 pt-md-5 codeContainer">
                        <p class="pb-40 ">Καταχώρησε τον κωδικό σου:</p>
                        <input type="text" placeholder="" name="code" class="round required " maxlength="8" v-model="form.code">
                        <span class="help is-danger " v-if="form.errors.has('code')" v-text="form.errors.get('code')"></span>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center pt-5 pb-5 formFields">
                <div class="col-md-10 col-lg-8">
                    <div class="row">
                        <div class="col-12 text-center pb-40 pt-5">
                            <p class="">Συμπλήρωσε τα στοιχεία σου:</p>
                        </div>
                        <div class="col-12 col-md-6 pb-3 ">
                            <input type="text" placeholder="ΟΝΟΜΑ*" name="name" class="required" v-model="form.name">
                            <span class="help is-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
                        </div>
                        <div class="col-12 col-md-6 pb-3 ">
                            <input type="text" placeholder="ΕΠΩΝΥΜΟ*" name="surname" class="required" v-model="form.surname">
                            <span class="help is-danger" v-if="form.errors.has('surname')" v-text="form.errors.get('surname')"></span>
                        </div>

                        <div class="col-12 col-md-6 pb-3 pb-md-0 ">
                            <input type="text" placeholder="EMAIL*" name="email" class="email" v-model="form.email">
                            <span class="help is-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></span>
                        </div>

                        <div class="col-12 col-md-6 ">
                            <input type="text" placeholder="ΤΗΛΕΦΩΝΟ*" name="tel" class="required" maxlength="10" v-model="form.tel">
                            <span class="help is-danger" v-if="form.errors.has('tel')" v-text="form.errors.get('tel')"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">

                <div class="col-md-10 col-lg-8 text-left">

                    <div class="checkbox pb5" v-bind:class="{ 'has-error': form.errors.has('opt') }">
                        <label class="" for="optinCheckbox">
                            <input type="checkbox" name="opt" id="optinCheckbox" v-model="form.opt" @change="form.errors.clear($event.target.name)">
                            <span class="custom-checkbox"><i class="icon-check"></i></span>
                            Έχω διαβάσει και αποδέχομαι τους <a href="/termsconditions" @click.prevent="termsOpen">όρους συμμετοχής.</a>
                        </label>
                    </div>

                    <div class=" callout alert hidden opt-alert">Πρέπει να αποδεχτείτε τους όρους συμμετοχής.</div>

                </div>

            </div>

            <div class="row">

                <div class="col-12 m-auto pt-5 pb-5 ">
                    <button class="button submit" id="submit" :disabled="form.errors.any()">ΥΠΟΒΟΛΗ <i v-if="form.sending" class="fas fa-spinner fa-spin"></i></button>
                </div>

            </div>

        </form>
    </participation-form>

</div>
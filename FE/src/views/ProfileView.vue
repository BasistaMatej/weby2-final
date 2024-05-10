<!-- POTREBUJEM GETNUT -> Meno, Priezvisko, Email a LastLogin -->

<template>
    <AuthNavBar />
    <div class="d-flex justify-content-center">
        <div class="d-flex align-items-center flex-column" id="box">
            <div class="d-flex justify-content-center" id="box-icon"
                style="background: linear-gradient(to bottom, #8B5CF6ee 0%, #8B5CF6ee 50%, transparent 50%, transparent 100%);">

                <lord-icon id="icon" src="https://cdn.lordicon.com/xcxzayqr.json" trigger="hover"
                    colors="primary:#692CF3,secondary:#f4c89c" style="width:250px;height:250px;">
                </lord-icon>

            </div>

            <h1 class="display-4 text-center"><lord-icon id="h1-icon" src="https://cdn.lordicon.com/kkvxgpti.json"
                    trigger="hover" delay="500" colors="primary:#0000004d" style="width:3rem;height:3rem">
                </lord-icon>SAMUEL KUBALA</h1>

            <h2 class="display-6 text-center"><lord-icon id="h2-icon" src="https://cdn.lordicon.com/yxczfiyc.json"
                    trigger="hover" style="width:2rem;height:2rem">
                </lord-icon>Level</h2>

            <h4 class="text-center"> <lord-icon class="icon-small" src="https://cdn.lordicon.com/nzixoeyk.json"
                    trigger="hover" delay="500" colors="primary:#696969" style="width:25px;height:25px">
                </lord-icon>kubalasamuel1a@gmail.com</h4>

            <h6 class="text-center"><lord-icon class="icon-small" src="https://cdn.lordicon.com/mwikjdwh.json"
                    trigger="hover" delay="500" colors="primary:#aaaaaa" style="width:20px;height:20px">
                </lord-icon>Last login</h6>

            <lord-icon src="https://cdn.lordicon.com/xyboiuok.json" trigger="loop" state="morph-heart" delay="1000"
                colors="primary:#a866ee" style="width:45px;height:45px">
            </lord-icon>

        </div>
    </div>
</template>

<script setup>
import AuthNavBar from '@/components/AuthNavBar.vue';
</script>

<style scoped>
.icon-small {
    padding-top: .25rem;
}

#h1-icon {
    padding-top: .2rem;
}

#h2-icon {
    padding-top: .25rem;
}

h2 {
    /* color: #696969;*/
    color: rgba(0, 0, 0, 0.7);
}

#box-icon {
    width: 100%;
    padding-top: 2vh;
    border-top-left-radius: 2rem;
    border-top-right-radius: 2rem;
}

h6 {
    /*color: #8B5CF6ee;*/
    color: rgba(0, 0, 0, 0.4);
}

#icon {
    border-radius: 50%;
    overflow: hidden;
    background-color: #f1f1f1ee;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

#box {
    background-color: white;
    margin-top: 5vh;
    border-radius: 2rem;
    width: 50%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

h1 {
    color: rgba(0, 0, 0, 0.8);
    margin-top: .5rem;
    margin-bottom: 0;
}

h4 {
    color: rgba(0, 0, 0, 0.6);
    /*color: rgb(214, 211, 211);*/
    text-decoration: underline dotted;
}

@media (max-width: 768px) {
    #box {
        width: 70%;
    }

    #h1-icon {
        padding-top: .25rem;
        width: 2.5rem !important;
        height: 2.5rem !important;
    }
}

@media (max-width: 520px) {
    #box {
        width: 90%;
    }

    #h1-icon {
        padding-top: .25rem;
        width: 2rem !important;
        height: 2rem !important;
    }
}

@media (max-width: 480px) {
    #icon {
        width: 170px !important;
        height: 170px !important;
    }
}
</style>
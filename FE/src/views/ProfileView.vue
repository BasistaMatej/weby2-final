<!-- POTREBUJEM GETNUT -> Meno, Priezvisko, Email a LastLogin -->

<template>
    <AuthNavBar />
    <div class="d-flex justify-content-center">
        <div class="d-flex align-items-center flex-column" id="box">
            <div class="d-flex justify-content-center" id="box-icon"
                style="background: linear-gradient(to bottom, #8B5CF6ee 0%, #8B5CF6ee 50%, transparent 50%, transparent 100%);">

                <lord-icon id="icon" src="https://cdn.lordicon.com/xcxzayqr.json" trigger="hover"
                    colors="primary:#692CF3,secondary:#f4c89c" style="width:14rem;height:14rem;">
                </lord-icon>

            </div>

            <h1 class="display-4 text-center mb-1">Ahoj Samuel!</h1>

            <div class="content-box">
                <p class="header-p">Level oprávnení</p>
                <p class="content-p">Admin</p>
            </div>


            <div class="content-box">
                <p class="header-p">Emailová adresa</p>
                <p class="content-p">kubalasamuel1a@gmail.com</p>
            </div>

            <div class="content-box">
                <p class="header-p">Posledná aktivita</p>
                <p class="content-p">12-04-2024 16:02</p>
            </div>

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
.content-box {
    width: 70%;
    border-bottom: 1px solid #aaaaaa;
    margin-left: 15%;
    margin-right: 15%;
    margin-top: 3%;
}

.header-p {
    color: #a866ee;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.content-p {
    margin-bottom: .5rem;
    font-size: 1.25rem;
}

#box-icon {
    width: 100%;
    padding-top: 2vh;
    border-top-left-radius: 2rem;
    border-top-right-radius: 2rem;
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

@media (max-width: 768px) {
    #box {
        width: 70%;
    }

    #icon {
        width: 10rem !important;
        height: 10rem !important;
    }

    .content-box {
        margin-left: 10%;
        margin-right: 10%;
        width: 80%;
    }
}

@media (max-width: 520px) {
    #box {
        width: 90%;
    }
}

@media (max-width: 480px) {
    #icon {
        width: 7rem !important;
        height: 7rem !important;
    }

    .content-p {
        font-size: 1rem;
    }
}
</style>
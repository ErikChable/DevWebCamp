import Swiper from "swiper";
import { Navigation } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.slider')) {
        const opciones = {
            modules: [Navigation],
            slidesPerView: 1, // Cuantas quieres que se muestren en pantalla
            spaceBetween: 15, // Espacio entre cada una
            freeMode: true, // Si no hacen efecto puedes colocar esta linea
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            breakpoints: { // Funciona como un MQ
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                }
            }
        }

        new Swiper('.slider', opciones)
    }
});
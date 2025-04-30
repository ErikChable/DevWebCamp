<main class="devwebcamp">
    <h2 class="devwebcamp__heading"><?php echo $titulo; ?></h2>
    <p class="devwebcamp__descripcion">Conoce la conferencia más importante de Latinoamérica</p>

    <div class="devwebcamp__grid">
        <div <?php aos_animacion(); ?> data-aos-once="true" class="devwebcamp__imagen">
            <picture>
                <source srcset="build/img/sobre_devwebcamp.avif" type="image/avif">
                <source srcset="build/img/sobre_devwebcamp.webp" type="image/webp">
                <img loading="lazy" width="200" height="300" src="build/img/sobre_devwebcamp.jpg" alt="Imagen DevWebCamp">
            </picture>
        </div>

        <div <?php aos_animacion(); ?> data-aos-once="true" class="devwebcamp__contenido">
            <p class="devwebcamp__texto">Lorem ipsum dolor sit amet consectetur adipisicing elit. Minus provident magni possimus fuga nostrum deleniti amet error modi praesentium distinctio, a, eligendi iusto eos et! Ipsa veniam numquam temporibus rem.
            </p>

            <p class="devwebcamp__texto">Lorem ipsum dolor sit amet consectetur adipisicing elit. Minus provident magni possimus fuga nostrum deleniti amet error modi praesentium distinctio, a, eligendi iusto eos et! Ipsa veniam numquam temporibus rem.
            </p>
        </div>
    </div>
</main>
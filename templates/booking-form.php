<?php 
$options = get_option('ez_booking_options');

if($_SERVER['REQUEST_METHOD'] == "POST"){
    
}?>

<div class="modal">
    <div class="modal__dialog">
        <div class="modal__header text-center">
            Reservation en ligne
        </div>
        <div class="modal__body">
            <div class="form__group">
                <div class="form__image">
                    <div class="form__image-inner">
                        <div class="form__image-empty">
                            <div class="form__image-text text-center">Télécharger une photo</div>
                            <div class="form__image-icon">
                                <svg width="71" height="71" viewBox="0 0 71 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23.6665 35.5H47.3332M35.4998 47.3333V23.6667M35.4998 65.0833C51.7707 65.0833 65.0832 51.7708 65.0832 35.5C65.0832 19.2292 51.7707 5.91667 35.4998 5.91667C19.229 5.91667 5.9165 19.2292 5.9165 35.5C5.9165 51.7708 19.229 65.0833 35.4998 65.0833Z" stroke="#171616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <img src="" alt="" class="form__image-avatar" />
                    </div>
                    <input type="file" name="avatar" id="form__avatar">
                </div>
                <div class="form__controls">
                    <div class="form__control">
                        <input type="text" name="name" placeholder="Nom, Prénom*" required class="form__field">
                    </div>
                    <div class="form__control">
                        <input type="text" name="birthday" placeholder="Date de naissance*" required class="form__field">
                    </div>
                    <div class="form__control">
                        <input type="text" name="phone" placeholder="Numéro de téléphone*" required class="form__field">
                    </div>
                </div>
            </div>
            <div class="form__control">
                <input type="email" name="email" placeholder="Email*" required class="form__field">
            </div>
            <div class="form__select">
                <div class="form__field form__field-label">Choisissez une prestation</div>
                <div class="form__select-options">
                    <div class="form__select-option form__select-option_plus" data-option="1">
                        <span>Massages du corps, 70€</span>
                    </div>
                    <div class="form__select-option form__select-option_check" data-option="2">
                        <span>Massages du corps, 70€</span>
                    </div>
                </div>
                <input type="hidden">
            </div>
            <div class="form__row">
                <div class="form__select form__select_calendar">
                    <div class="form__field form__field-label">Sélecteur de date*</div>
                    <div class="form__select-options">
                        <div class="form__select-option" data-option="1">
                            <span>20.12.2022</span>
                        </div>
                        <div class="form__select-option" data-option="2">
                            <span>21.12.2022</span>
                        </div>
                    </div>
                    <input type="hidden">
                </div>
                <div class="form__select form__select_clock">
                    <div class="form__field form__field-label">Choisissez une heure*</div>
                    <div class="form__select-options">
                        <div class="form__select-option" data-option="1">
                            <span>10.00-11.00</span>
                        </div>
                        <div class="form__select-option" data-option="2">
                            <span>11.00-12.00</span>
                        </div>
                    </div>
                    <input type="hidden">
                </div>
            </div>
            <div class=" form__control">
                <label class="form__label"><?php echo esc_attr( $options['question_1'] );?></label>
                <input type="text" name="response" placeholder="Entrez votre réponse*" required class="form__field">
            </div>
            <div class="form__control">
                <label class="form__label"><?php echo esc_attr( $options['question_2'] );?></label>
                <input type="text" name="email" placeholder="Entrez votre réponse*" required class="form__field">
            </div>
            <div class="form__control">
                <label class="form__label"><?php echo esc_attr( $options['question_3'] );?></label>
                <input type="text" name="email" placeholder="Entrez votre réponse*" required class="form__field">
            </div>
            <div class="form__control">
                <label class="form__label"><?php echo esc_attr( $options['question_4'] );?></label>
                <input type="text" name="email" placeholder="Entrez votre réponse*" required class="form__field">
            </div>
            <div class="form__control">
                <label class="form__label"><?php echo esc_attr( $options['question_5'] );?></label>
                <input type="text" name="email" placeholder="Entrez votre réponse*" required class="form__field">
            </div>
            <div class="form__control form__control_margin-small">
                <textarea name="email" placeholder="Commentaire" class="form__field"></textarea>
            </div>
            <div class="form__control form__control_margin-small">
                <div class="form__checkbox">
                    <input type="checkbox" id="happy" name="happy" value="yes">
                    <label for="happy">J'accepte le traitement des données personnelles*</label>
                </div>
            </div>
            <div class="form__buttons">
                <button type="submit" class="form__button">Annulation</button>
                <button type="button" class="form__button form__button_cancel">Envoyer</button>
            </div>
        </div>
    </div>
</div>

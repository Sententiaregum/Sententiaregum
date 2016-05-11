/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import AppDispatcher from '../dispatcher/AppDispatcher';
import LocaleConstants from '../constants/Locale';
import Locale from '../util/http/LocaleService';
import ApiKey from '../util/http/ApiKeyService';
import LocaleStore from '../store/LocaleStore';
import LocaleWebAPIUtils from '../util/api/LocaleWebAPIUtils';

/**
 * Action creator which dispatches actions for the locale switcher.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleActions {
  /**
   * Loads supported items and dispatch them to the store.
   *
   * @returns {void}
   */
  loadLanguages() {
    if (!LocaleStore.isInitialized()) {
      LocaleWebAPIUtils.getLocales(response => {
        AppDispatcher.dispatch({
          event:  LocaleConstants.GET_LOCALES,
          result: response
        });
      });
    } else {
      LocaleStore.triggerLocaleChange();
    }
  }

  /**
   * Changes the locale.
   *
   * @param {string} locale The new locale.
   *
   * @returns {void}
   */
  changeLocale(locale) {
    Locale.setLocale(locale);

    if (ApiKey.isLoggedIn()) {
      LocaleWebAPIUtils.changeUserLocale(locale);
    }
  }
}

export default new LocaleActions();

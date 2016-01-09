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

import sinon from 'sinon';
import LocaleActions from '../../actions/LocaleActions';
import chai from 'chai';
import AppDispatcher from '../../dispatcher/AppDispatcher';
import LocaleConstants from '../../constants/Locale';
import {ApiKey, Locale} from '../../util/http/facade/HttpServices';
import Cookies from 'cookies-js';
import LocaleStore from '../../store/LocaleStore';
import LocaleWebAPIUtils from '../../util/api/LocaleWebAPIUtils';

describe('LocaleActions', () => {
  it('changes the locale', () => {
    let apiKey = Math.random();

    sinon.createStubInstance(Cookies);
    sinon.stub(ApiKey, 'isLoggedIn', () => true);
    sinon.stub(ApiKey, 'getApiKey', () => apiKey);
    sinon.stub(Locale, 'setLocale', (locale) => chai.expect(locale).to.equal('en'));

    sinon.stub(LocaleWebAPIUtils, 'changeUserLocale', (locale) => {
      chai.expect(locale).to.equal('en');
    });

    LocaleActions.changeLocale('en');

    sinon.assert.calledOnce(LocaleWebAPIUtils.changeUserLocale);

    ApiKey.isLoggedIn.restore();
    ApiKey.getApiKey.restore();
    Locale.setLocale.restore();
    LocaleWebAPIUtils.changeUserLocale.restore();
  });

  it('avoids locale change if store is already initialized', () => {
    sinon.stub(LocaleStore, 'isInitialized', () => true);
    sinon.stub(LocaleStore, 'triggerLocaleChange');
    sinon.stub(LocaleWebAPIUtils, 'getLocales');

    LocaleActions.loadLanguages();
    sinon.assert.calledOnce(LocaleStore.triggerLocaleChange);
    sinon.assert.notCalled(LocaleWebAPIUtils.getLocales);

    LocaleStore.isInitialized.restore();
    LocaleStore.triggerLocaleChange.restore();
    LocaleWebAPIUtils.getLocales.restore();
  });

  it('loads available locales', () => {
    let response = {de:'Deutsch',en:'English'};

    sinon.stub(AppDispatcher, 'dispatch', (payload) => {
      chai.expect(payload.event).to.equal(LocaleConstants.GET_LOCALES);
      chai.expect(payload.result).to.equal(response);
    });

    sinon.stub(LocaleWebAPIUtils, 'getLocales', (callable) => {
      callable.apply(this, [response]);
    });

    LocaleActions.loadLanguages();

    AppDispatcher.dispatch.restore();
    LocaleWebAPIUtils.getLocales.restore();
  });
});

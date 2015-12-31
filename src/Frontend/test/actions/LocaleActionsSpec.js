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
import $ from 'jquery';
import AppDispatcher from '../../dispatcher/AppDispatcher';
import LocaleConstants from '../../constants/Locale';
import {ApiKey, Locale} from '../../util/http/facade/HttpServices';
import Cookies from 'cookies-js';
import LocaleStore from '../../store/LocaleStore';

describe('LocaleActions', () => {
  it('changes the locale', () => {
    let apiKey = Math.random();

    sinon.createStubInstance(Cookies);
    sinon.stub(ApiKey, 'isLoggedIn', () => true);
    sinon.stub(ApiKey, 'getApiKey', () => apiKey);
    sinon.stub(Locale, 'setLocale', (locale) => chai.expect(locale).to.equal('en'));

    $.ajax = function () {};
    sinon.stub($, 'ajax', (payload) => {
      chai.expect(payload.url).to.equal('/api/protected/locale.json');
      chai.expect(payload.method).to.equal('PATCH');
      chai.expect(payload.data.locale).to.equal('en');
      chai.expect(payload.headers['X-API-KEY']).to.equal(apiKey);
    });

    LocaleActions.changeLocale('en');

    ApiKey.isLoggedIn.restore();
    ApiKey.getApiKey.restore();
    Locale.setLocale.restore();
  });

  it('avoids locale change if store is already initialized', () => {
    $.ajax = function () {};

    sinon.stub(LocaleStore, 'isInitialized', () => true);
    sinon.stub(LocaleStore, 'triggerLocaleChange');
    sinon.stub($, 'ajax');

    LocaleActions.loadLanguages();
    sinon.assert.calledOnce(LocaleStore.triggerLocaleChange);
    sinon.assert.notCalled($.ajax);

    LocaleStore.isInitialized.restore();
    LocaleStore.triggerLocaleChange.restore();
    $.ajax.restore();
  });

  it('loads available locales', () => {
    let response = {de:'Deutsch',en:'English'};

    sinon.stub(AppDispatcher, 'dispatch', (payload) => {
      chai.expect(payload.event).to.equal(LocaleConstants.GET_LOCALES);
      chai.expect(payload.result).to.equal(response);
    });

    $.ajax = function () {};
    sinon.stub($, 'ajax', (payload) => {
      chai.expect(payload.url).to.equal('/api/locale.json');
      chai.expect(payload.method).to.equal('GET');
      payload.success.apply(null, [response]);
    });

    LocaleActions.loadLanguages();

    $.ajax.restore();
    AppDispatcher.dispatch.restore();
  });
});

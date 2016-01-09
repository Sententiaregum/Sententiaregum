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

import LocaleService from '../../../util/http/LocaleService';
import CookieFactory from '../../../util/http/CookieFactory';
import sinon from 'sinon';
import chai from 'chai';
import jsdom from 'jsdom';
import counterpart from 'counterpart';

describe('LocaleService', () => {
  it('returns default locale if cookie store is empty', () => {
    let window          = jsdom.jsdom().parentWindow;
    let factoryInstance = new CookieFactory(window);
    let mock            = {
      get: () => null
    };

    sinon.stub(factoryInstance, 'getCookies', () => mock);
    let instance = new LocaleService('en', factoryInstance);

    chai.expect(instance.getLocale()).to.equal('en');

    factoryInstance.getCookies.restore();
  });

  it('fetches locale from cookie store', () => {
    let window          = jsdom.jsdom().parentWindow;
    let factoryInstance = new CookieFactory(window);
    let mock            = {
      get: () => 'de'
    };

    sinon.stub(factoryInstance, 'getCookies', () => mock);
    let instance = new LocaleService('en', factoryInstance);

    chai.expect(instance.getLocale()).to.equal('de');

    factoryInstance.getCookies.restore();
  });

  it('sets default locale', () => {
    let spy             = sinon.spy();
    let window          = jsdom.jsdom().parentWindow;
    let factoryInstance = new CookieFactory(window);
    let mock            = {
      get: () => null,
      set: spy
    };

    sinon.stub(factoryInstance, 'getCookies', () => mock);
    let instance = new LocaleService('en', factoryInstance);
    instance.setLocale(null);

    chai.expect(spy.calledOnce).to.equals(true);
    chai.expect(spy.calledWith('language', 'en')).to.equals(true);
  });

  it('throws error on invalid languages', () => {
    let instance = new LocaleService('en');

    chai.expect(
      () => instance.setLocale('fr')
    ).to.throw('[LocaleService.setLocale(fr)] Invalid locale! Allowed locales are de,en!')
  });
});

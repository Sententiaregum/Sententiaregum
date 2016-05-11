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
import chai from 'chai';
import Cookies from 'cookies-js';
import LocaleService from '../../../util/http/LocaleService';

describe('LocaleService', () => {
  it('returns default locale if cookie store is empty', () => {
    sinon.stub(Cookies, 'get', () => null);
    chai.expect(LocaleService.getLocale()).to.equal('en');
    Cookies.get.restore();
  });

  it('fetches locale from local store', () => {
    sinon.stub(Cookies, 'get', () => 'de');

    chai.expect(LocaleService.getLocale()).to.equal('de');
    Cookies.get.restore();
  });

  it('sets default locale', () => {
    sinon.stub(Cookies, 'get', () => null);
    sinon.stub(Cookies, 'set');

    LocaleService.setLocale(null);
    chai.expect(Cookies.set.calledWith('language', 'en')).to.equals(true);
    Cookies.get.restore();
    Cookies.set.restore();
  });

  it('throws error on invalid languages', () => {
    chai.expect(
      () => LocaleService.setLocale('fr')
    ).to.throw('[LocaleService.setLocale(fr)] Invalid locale! Allowed locales are de,en!')
  });
});

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

import { expect } from 'chai';
import { stub } from 'sinon';
import Cookies from 'cookies-js';
import LocaleService from '../../../util/http/LocaleService';

describe('LocaleService', () => {
  it('returns default locale if cookie store is empty', () => {
    stub(Cookies, 'get', () => null);
    expect(LocaleService.getLocale()).to.equal('en');
    Cookies.get.restore();
  });

  it('fetches locale from local store', () => {
    stub(Cookies, 'get', () => 'de');

    expect(LocaleService.getLocale()).to.equal('de');
    Cookies.get.restore();
  });

  it('sets default locale', () => {
    stub(Cookies, 'get', () => null);
    stub(Cookies, 'set');

    LocaleService.setLocale(null);
    expect(Cookies.set.calledWith('language', 'en')).to.equals(true);
    Cookies.get.restore();
    Cookies.set.restore();
  });

  it('throws error on invalid languages', () => {
    expect(
      () => LocaleService.setLocale('fr')
    ).to.throw('[LocaleService.setLocale(fr)] Invalid locale! Allowed locales are de,en!')
  });
});

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

import LanguageSwitcher from '../../../../components/app/widgets/LanguageSwitcher';
import LocaleStore from '../../../../store/LocaleStore';
import sinon from 'sinon';
import chai from 'chai';
import Cookies from 'cookies-js';
import { Locale } from '../../../../util/http/facade/HttpServices';
import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import React from 'react';

describe('LanguageSwitcher', () => {
  it('renders locales', () => {
    let clock  = sinon.useFakeTimers();
    sinon.createStubInstance(Cookies);
    sinon.stub(Locale, 'getLocale', () => 'en');

    const locales = {
      de: 'Deutsch',
      en: 'English'
    };

    sinon.stub(LocaleStore, 'getAllLocales', () => locales);
    sinon.stub(LocaleStore, 'isInitialized', () => true);

    const result = TestUtils.renderIntoDocument(<LanguageSwitcher />);
    clock.tick(1000);
    const cmp = ReactDOM.findDOMNode(result);

    const dropdown = cmp._childNodes[1]._childNodes;

    chai.expect(dropdown.length).to.equal(2);
    chai.expect(dropdown[0]._childNodes[0]._attributes.id._nodeValue).to.equal('de');
    chai.expect(dropdown[0]._childNodes[0]._childNodes[0]._nodeValue).to.equal('Deutsch');

    chai.expect(dropdown[1]._childNodes[0]._attributes.id._nodeValue).to.equal('en');
    chai.expect(dropdown[1]._attributes.class._nodeValue).to.equal('active');
    chai.expect(dropdown[1]._childNodes[0]._childNodes[0]._nodeValue).to.equal('English');

    LocaleStore.getAllLocales.restore();
    LocaleStore.isInitialized.restore();
    clock.restore();
  });

  it('shows loading bar until locales were loaded', () => {
    const result = TestUtils.renderIntoDocument(<LanguageSwitcher />);
    const node   = ReactDOM.findDOMNode(result);

    chai.expect(node._childNodes[1]._childNodes[0]._childNodes[0]._childNodes[0]._childNodes[0]._childNodes[0]._nodeValue).to.equal('Loading languages...');
  });
});

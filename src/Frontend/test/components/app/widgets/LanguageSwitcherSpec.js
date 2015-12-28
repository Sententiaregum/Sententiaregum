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
import {Locale} from '../../../../util/http/facade/HttpServices';

describe('LanguageSwitcher', () => {
  it('renders locales', () => {
    sinon.createStubInstance(Cookies);
    sinon.stub(Locale, 'getLocale', () => 'en');

    const locales = {
      de: 'Deutsch',
      en: 'English'
    };

    sinon.stub(LocaleStore, 'getAllLocales', () => locales);

    const component = new LanguageSwitcher({});
    sinon.stub(component, 'setState', (change) => {
      component.state.locales = change.locales;
    });
    component.refreshLocales();

    const result   = component.render();
    const dropdown = result.props.children;

    chai.expect(dropdown.length).to.equal(2);
    chai.expect(dropdown[0].props.id).to.equal('de');
    chai.expect(dropdown[0].props.children).to.equal('Deutsch');

    chai.expect(dropdown[1].props.id).to.equal('en');
    chai.expect(dropdown[1].props.className).to.equal('active');
    chai.expect(dropdown[1].props.children).to.equal('English');

    LocaleStore.getAllLocales.restore();
  });

  it('shows loading bar until locales were loaded', () => {
    const component = new LanguageSwitcher({});

    const result = component.render();
    const bar    = result.props.children;

    chai.expect(bar.props.children.props.children.props.content).to.equal('menu.l10n_loading');
  });
});

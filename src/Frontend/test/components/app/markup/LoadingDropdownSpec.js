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
import LoadingDropDown from '../../../../components/app/markup/LoadingDropDown';
import React from 'react';
import { shallow } from 'enzyme';

describe('LoadingDropDown', () => {
  it('renders a loading dropdown', () => {
    const markup = shallow(<LoadingDropDown translationContent="menu.l10n_loading" />);
    expect(markup.hasClass('languageLoader')).to.equal(true);

    const span = markup.find('span');
    expect(span.hasClass('loading')).to.equal(true);
    expect(span.find('Translate').prop('content')).to.equal('menu.l10n_loading');
  });
});

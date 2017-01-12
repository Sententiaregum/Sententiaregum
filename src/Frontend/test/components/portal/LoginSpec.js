/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Login from '../../../components/portal/Login';
import InfoBox from '../../../components/portal/login/InfoBox';
import Form from '../../../components/portal/login/Form';
import React from 'react';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('Login', () => {
  it('renders login page and info box', () => {
    const markup = shallow(<Login />);

    expect(markup.find('div > h1 > Translate').prop('content')).to.equal('pages.portal.login.headline');
    expect(markup.find('div > Grid > Row > [className="grid-item-2"] > Panel').contains(<InfoBox />));
    expect(markup.find('div > Grid > Row > [className="grid-item-1"] > Panel').contains(<Form />));
  });
});

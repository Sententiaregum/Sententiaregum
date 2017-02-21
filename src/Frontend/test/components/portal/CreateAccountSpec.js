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

import CreateAccount from '../../../components/portal/CreateAccount';
import React from 'react';
import { expect } from 'chai';
import { shallow } from 'enzyme';
import Form from '../../../components/portal/signup/Form';
import InfoBox from '../../../components/portal/signup/InfoBox';

describe('CreateAccount', () => {
  it('renders registration page', () => {
    const markup = shallow(<CreateAccount />);
    expect(markup.find('h1 Translate').prop('content')).to.equal('pages.portal.head');

    const body = markup.find('div');
    expect(body.contains(<Form />)).to.equal(true);
    expect(body.contains(<InfoBox />)).to.equal(true);
  });
});

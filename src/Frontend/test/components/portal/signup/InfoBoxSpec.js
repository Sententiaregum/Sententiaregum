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

import React from 'react';
import InfoBox from '../../../../components/portal/signup/InfoBox';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('InfoBox', () => {
  it('renders information for registration page', () => {
    const markup = shallow(<InfoBox />);
    expect(markup.find('Translate').prop('content')).to.equal('pages.portal.create_account.info_box');
    expect(markup.prop('bsStyle')).to.equal('info');
  });
});

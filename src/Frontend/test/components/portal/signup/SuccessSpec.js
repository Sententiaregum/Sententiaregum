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
import Success from '../../../../components/portal/signup/Success';
import { expect } from 'chai';
import { shallow } from 'enzyme';

describe('Success', () => {
  it('renders success box', () => {
    const markup = shallow(<Success />);
    expect(markup.find('Translate').prop('content')).to.equal('pages.portal.create_account.success');
    expect(markup.prop('bsStyle')).to.equal('success');
  });
});

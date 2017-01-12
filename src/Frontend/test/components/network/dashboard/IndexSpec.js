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
import { shallow } from 'enzyme';
import Index from '../../../../components/network/dashboard/Index';
import { expect } from 'chai';

describe('Index', () => {
  it('renders the title into the markup', () => {
    const markup = shallow(<Index />);
    expect(markup.find('h1 > Translate').prop('content')).to.equal('pages.network.dashboard.index.title');
  });
});
